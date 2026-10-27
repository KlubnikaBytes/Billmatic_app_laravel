<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManagedUser;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportEmailController extends Controller
{
    public function emailReportToCA(Request $request)
    {
        $validated = $request->validate([
            'report_type' => [
                'required',
                'in:bill-wise-profit,sales-summary,daybook,profit-loss,balance-sheet',
            ],
        ]);

        $user = $request->user();

       
        // Find all CAs registered by this business owner.
        $cas = ManagedUser::where('owner_user_id', $user->id)
            ->where('role', 'CA')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($cas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Please add a CA with an email address in Manage Users.',
            ], 422);
        }


        // Reuse the existing report calculation methods.
        $invoiceController = app(InvoiceController::class);

        $reportResponse = match ($validated['report_type']) {
            'bill-wise-profit' => $invoiceController->billWiseProfit($request),
            'sales-summary' => $invoiceController->salesSummary($request),
            'daybook' => $invoiceController->daybook($request),
            'profit-loss' => $invoiceController->profitLoss($request),
            'balance-sheet' => $invoiceController->balanceSheet($request),
        };

        $payload = $reportResponse->getData(true);

        if (
            $reportResponse->getStatusCode() !== 200 ||
            !($payload['success'] ?? false)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to generate the report for email.',
            ], 422);
        }

        $data = $payload['data'] ?? [];

        $titles = [
            'bill-wise-profit' => 'Bill Wise Profit',
            'sales-summary' => 'Sales Summary',
            'daybook' => 'Daybook',
            'profit-loss' => 'Profit and Loss',
            'balance-sheet' => 'Balance Sheet',
        ];

        $title = $titles[$validated['report_type']];

        // The row-list key differs between report types.
        $rowsKey = in_array(
            $validated['report_type'],
            ['daybook']
        ) ? 'entries' : 'invoices';

        $rows = $data[$rowsKey] ?? [];

        if (!is_array($rows)) {
            $rows = [];
        }

        // Summary contains the totals and selected dates.
        $summary = $data;
        unset($summary['invoices'], $summary['entries']);

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8">
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
            h2 { text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
            th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
            th { background: #eeeeff; }
        </style></head><body>';

        $html .= '<h2>' . e($title) . '</h2>';
        $html .= '<p>Business: ' . e($user->name ?? 'Billnika User') . '</p>';

        if (!empty($summary)) {
            $html .= '<h3>Summary</h3><table>';

            foreach ($summary as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    continue;
                }

                $html .= '<tr><th>' .
                    e(ucwords(str_replace('_', ' ', $key))) .
                    '</th><td>' . e((string) ($value ?? '-')) .
                    '</td></tr>';
            }

            $html .= '</table>';
        }

        if (!empty($rows)) {
            $columns = [];

            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }

                foreach (array_keys($row) as $column) {
                    if (!in_array($column, $columns, true)) {
                        $columns[] = $column;
                    }
                }
            }

            if (!empty($columns)) {
                $html .= '<h3>Report Details</h3><table><thead><tr>';

                foreach ($columns as $column) {
                    $html .= '<th>' .
                        e(ucwords(str_replace('_', ' ', $column))) .
                        '</th>';
                }

                $html .= '</tr></thead><tbody>';

                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }

                    $html .= '<tr>';

                    foreach ($columns as $column) {
                        $value = $row[$column] ?? '-';

                        if (is_array($value) || is_object($value)) {
                            $value = json_encode($value);
                        }

                        $html .= '<td>' . e((string) $value) . '</td>';
                    }

                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
            }
        }

        $html .= '</body></html>';

        $pdfBytes = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->output();

        $fileName = str_replace(
            ' ',
            '_',
            strtolower($title)
        ) . '.pdf';

       
        // Send the PDF report to every registered CA with an email.
        $sentEmails = [];

        foreach ($cas as $ca) {
            Mail::raw(
                "Dear CA,\n\nPlease find attached the {$title} report for the business.\n\nRegards,\nBillnika",
                function ($message) use ($ca, $title, $pdfBytes, $fileName) {
                    $message->to($ca->email)
                        ->subject('Billnika Report: ' . $title)
                        ->attachData(
                            $pdfBytes,
                            $fileName,
                            ['mime' => 'application/pdf']
                        );
                }
            );

            $sentEmails[] = $ca->email;
        }

        return response()->json([
            'success' => true,
            'message' => $title . ' report sent to the registered CA email addresses.',
            'emails' => $sentEmails,
        ]);


        return response()->json([
            'success' => true,
            'message' => $title . ' report emailed to your CA.',
            'email' => $ca->email,
        ]);
    }
}
