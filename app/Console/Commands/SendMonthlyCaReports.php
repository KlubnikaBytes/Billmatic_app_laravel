<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\InvoiceController;
use App\Models\ManagedUser;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\CarbonImmutable;
use Throwable;

class SendMonthlyCaReports extends Command
{
    protected $signature = 'ca:send-monthly-reports {--month= : Report month YYYY-MM} {--force : Retry reports already sent}';
    protected $description = 'Email selected previous-month CA reports as PDFs, with per-report delivery tracking';

    private const TITLES = [
        'gstr1' => 'GSTR-1 (Sales)',
        'gstr2' => 'GSTR-2 (Purchase)',
        'bill-wise-profit' => 'Bill Wise Profit',
        'sales-summary' => 'Sales Summary',
        'profit-loss' => 'Profit and Loss',
        'balance-sheet' => 'Balance Sheet',
    ];

    public function handle(): int
    {
        if (!Schema::hasTable('ca_sharing_settings') || !Schema::hasTable('ca_report_deliveries')) {
            $this->error('Run php artisan migrate first.');
            return self::FAILURE;
        }

        $monthOption = $this->option('month');
        if ($monthOption && !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $monthOption)) {
            $this->error('Use --month=YYYY-MM');
            return self::FAILURE;
        }
        $period = $monthOption
            ? CarbonImmutable::createFromFormat('!Y-m-d', $monthOption . '-01')
            : CarbonImmutable::now(config('app.timezone'))->startOfMonth()->subMonth();
        $start = $period->startOfMonth()->toDateString();
        $end = $period->endOfMonth()->toDateString();
        $month = $period->format('Y-m');
        $failures = 0;

        DB::table('ca_sharing_settings')->where('enabled', true)->orderBy('user_id')->chunk(100, function ($settings) use ($month, $start, $end, &$failures) {
            foreach ($settings as $setting) {
                $owner = User::find($setting->user_id);
                $ca = ManagedUser::where('owner_user_id', $setting->user_id)
                    ->where('role', 'CA')->where('id', $setting->ca_managed_user_id)->first();
                if (!$owner || !$ca || !filter_var($ca->email, FILTER_VALIDATE_EMAIL)) {
                    $this->warn("Owner {$setting->user_id}: missing owner or selected CA email; skipped.");
                    $failures++;
                    continue;
                }
                $selected = json_decode($setting->reports ?: '[]', true);
                foreach (array_unique(is_array($selected) ? $selected : []) as $type) {
                    if (!isset(self::TITLES[$type])) continue;
                    $key = ['user_id' => $owner->id, 'ca_managed_user_id' => $ca->id,
                        'report_type' => $type, 'report_month' => $month];
                    $previous = DB::table('ca_report_deliveries')->where($key)->first();
                    if ($previous && $previous->status === 'sent' && !$this->option('force')) continue;
                    try {
                        $data = $this->reportData($owner, $type, $start, $end);
                        $pdf = Pdf::loadHTML($this->html(self::TITLES[$type], $month, $data))
                            ->setPaper('a4', 'landscape')->output();
                        $filename = Str::slug($type) . '-' . $month . '.pdf';
                        Mail::raw("Dear {$ca->name},\n\nAttached is the " . self::TITLES[$type]
                            . " report for {$month}.\n\nRegards,\nBillnika", function ($message) use ($ca, $type, $month, $pdf, $filename) {
                            $message->to($ca->email)
                                ->subject('Billnika: ' . self::TITLES[$type] . ' - ' . $month)
                                ->attachData($pdf, $filename, ['mime' => 'application/pdf']);
                        });
                        DB::table('ca_report_deliveries')->updateOrInsert($key, [
                            'status' => 'sent', 'recipient_email' => $ca->email,
                            'error_message' => null, 'sent_at' => now(), 'updated_at' => now(),
                        ]);
                        $this->info("Sent {$type} for owner {$owner->id} to {$ca->email}");
                    } catch (Throwable $e) {
                        $failures++;
                        DB::table('ca_report_deliveries')->updateOrInsert($key, [
                            'status' => 'failed', 'recipient_email' => $ca->email,
                            'error_message' => Str::limit($e->getMessage(), 2000),
                            'sent_at' => null, 'updated_at' => now(),
                        ]);
                        Log::error('CA monthly report failed', ['owner_id' => $owner->id,
                            'report' => $type, 'error' => $e->getMessage()]);
                        $this->error("Failed {$type} for owner {$owner->id}: {$e->getMessage()}");
                    }
                }
            }
        });
        return $failures ? self::FAILURE : self::SUCCESS;
    }

    private function reportData(User $owner, string $type, string $start, string $end): array
    {
        // GSTR-2 must be based on purchase tax lines, not sales invoices.
        // Do not email a misleading tax report until its purchase-side generator is wired.
        if ($type === 'gstr2') {
            throw new \RuntimeException('GSTR-2 purchase report generator is not connected yet.');
        }
        $request = Request::create('/api/ca-monthly-report', 'GET', [
            'range' => 'custom', 'start' => $start, 'end' => $end,
            'from' => $start, 'to' => $end, 'as_of' => $end,
            'status' => 'all',
        ]);
        $request->setUserResolver(fn () => $owner);
        $controller = app(InvoiceController::class);
        $response = match ($type) {
            'gstr1' => $controller->gstReport($request),
            'bill-wise-profit' => $controller->billWiseProfit($request),
            'sales-summary' => $controller->salesSummary($request),
            'profit-loss' => $controller->profitLoss($request),
            'balance-sheet' => $controller->balanceSheet($request),
        };
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Report API returned HTTP ' . $response->getStatusCode());
        }
        $payload = $response->getData(true);
        if (($payload['success'] ?? true) === false) {
            throw new \RuntimeException($payload['message'] ?? 'Report generation failed');
        }
        return $type === 'gstr1' ? $payload : ($payload['data'] ?? []);
    }

    private function html(string $title, string $month, array $data): string
    {
        $html = '<html><head><meta charset="UTF-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:10px}h2{color:#5143cc}table{width:100%;border-collapse:collapse;margin:12px 0}th,td{border:1px solid #bbb;padding:6px;vertical-align:top;word-break:break-word}th{background:#eeeaff}</style></head><body>';
        $html .= '<h2>' . e($title) . '</h2><p>Reporting month: ' . e($month) . '</p>';
        $render = function (array $values, int $depth = 0) use (&$render): string {
            if ($depth > 4) return '';
            $out = '<table><tbody>';
            foreach ($values as $key => $value) {
                $out .= '<tr><th>' . e((string)$key) . '</th><td>';
                $out .= is_array($value) ? $render($value, $depth + 1) : e((string)($value ?? '-'));
                $out .= '</td></tr>';
            }
            return $out . '</tbody></table>';
        };
        return $html . $render($data) . '</body></html>';
    }
}
