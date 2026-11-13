<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProformaInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProformaInvoiceController extends Controller
{
    // GET /api/proforma-invoices/next-number
    public function nextNumber(Request $request)
    {
        $lastId = ProformaInvoice::where(
            'user_id',
            $request->user()->id
        )->max('id');

        return response()->json([
            'success' => true,
            'next_number' => 'PI-' . (($lastId ?? 0) + 1),
        ]);
    }

    // GET /api/proforma-invoices
    public function index(Request $request)
    {
        $query = ProformaInvoice::where(
            'user_id',
            $request->user()->id
        )->with('party');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'proforma_number',
                    'like',
                    "%{$search}%"
                )->orWhereHas('party', function ($p) use ($search) {
                    $p->where(
                        'party_name',
                        'like',
                        "%{$search}%"
                    );
                });
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderByDesc('proforma_date')
                ->orderByDesc('id')
                ->paginate(20),
        ]);
    }

    // GET /api/proforma-invoices/{id}
    public function show(Request $request, int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->findProforma(
                $request->user()->id,
                $id
            ),
        ]);
    }

    // POST /api/proforma-invoices
    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $data = $this->validateData($request, $userId);

        $proforma = DB::transaction(function () use (
            $userId,
            $data
        ) {
            $totals = $this->calculateTotals($data);

            $date = Carbon::parse($data['proforma_date']);
            $validityDays = (int) (
                $data['validity_days'] ?? 7
            );

            $proforma = ProformaInvoice::create([
                'user_id' => $userId,
                'party_id' => $data['party_id'],
                'proforma_number' => 'TMP-' . Str::uuid(),
                'proforma_date' => $date->toDateString(),
                'validity_days' => $validityDays,
                'expiry_date' => $date
                    ->copy()
                    ->addDays($validityDays)
                    ->toDateString(),
                'notes' => $data['notes'] ?? null,
                'status' => 'created',
                ...$totals['header'],
            ]);

            $proforma->update([
                'proforma_number' => 'PI-' . $proforma->id,
            ]);

            $this->saveLines(
                $proforma,
                $totals['items'],
                $data['additional_charges'] ?? []
            );

            return $proforma;
        });

        return response()->json([
            'success' => true,
            'message' => 'Proforma Invoice created successfully',
            'data' => $this->findProforma(
                $userId,
                $proforma->id
            ),
        ], 201);
    }

    // PUT /api/proforma-invoices/{id}
    public function update(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $data = $this->validateData($request, $userId);

        DB::transaction(function () use (
            $userId,
            $id,
            $data
        ) {
            $proforma = ProformaInvoice::where(
                'user_id',
                $userId
            )->lockForUpdate()->findOrFail($id);

            $this->ensureEditable($proforma);

            $totals = $this->calculateTotals($data);

            $date = Carbon::parse($data['proforma_date']);
            $validityDays = (int) (
                $data['validity_days'] ?? 7
            );

            $proforma->update([
                'party_id' => $data['party_id'],
                'proforma_date' => $date->toDateString(),
                'validity_days' => $validityDays,
                'expiry_date' => $date
                    ->copy()
                    ->addDays($validityDays)
                    ->toDateString(),
                'notes' => $data['notes'] ?? null,
                ...$totals['header'],
            ]);

            $proforma->items()->delete();
            $proforma->additionalCharges()->delete();

            $this->saveLines(
                $proforma,
                $totals['items'],
                $data['additional_charges'] ?? []
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Proforma Invoice updated successfully',
            'data' => $this->findProforma($userId, $id),
        ]);
    }

    // DELETE /api/proforma-invoices/{id}
    public function destroy(Request $request, int $id)
    {
        DB::transaction(function () use ($request, $id) {
            $proforma = ProformaInvoice::where(
                'user_id',
                $request->user()->id
            )->lockForUpdate()->findOrFail($id);

            $this->ensureEditable($proforma);

            $proforma->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Proforma Invoice deleted successfully',
        ]);
    }

    private function ensureEditable(
        ProformaInvoice $proforma
    ): void {
        if (
            $proforma->status === 'converted' ||
            $proforma->converted_invoice_id !== null
        ) {
            throw ValidationException::withMessages([
                'proforma' =>
                    'Converted Proforma Invoices cannot be edited or deleted.',
            ]);
        }
    }

    private function validateData(
        Request $request,
        int $userId
    ): array {
        return $request->validate([
            'party_id' => [
                'required',
                'integer',
                Rule::exists('parties', 'id')
                    ->where('user_id', $userId),
            ],

            'proforma_date' => 'required|date',
            'validity_days' => 'nullable|integer|min:1|max:365',
            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',

            'items.*.item_id' => [
                'nullable',
                'integer',
                Rule::exists('items', 'id')
                    ->where('user_id', $userId),
            ],

            'items.*.item_name' =>
                'required|string|max:255',

            'items.*.hsn_code' =>
                'nullable|string|max:50',

            'items.*.unit' =>
                'nullable|string|max:30',

            'items.*.quantity' =>
                'required|numeric|gt:0',

            'items.*.rate' =>
                'required|numeric|min:0',

            'items.*.tax_percent' =>
                'nullable|numeric|between:0,100',

            'additional_charges' => 'nullable|array',

            'additional_charges.*.name' =>
                'required|string|max:255',

            'additional_charges.*.amount' =>
                'required|numeric|min:0',

            'discount_percent' =>
                'nullable|numeric|between:0,100',

            'discount_amount' =>
                'nullable|numeric|min:0',

            'round_off' => 'nullable|numeric',
        ]);
    }

    private function calculateTotals(array $data): array
    {
        $subtotal = 0;
        $totalTax = 0;
        $items = [];

        foreach ($data['items'] as $input) {
            $quantity = round(
                (float) $input['quantity'],
                3
            );

            $rate = round(
                (float) $input['rate'],
                2
            );

            $taxPercent = (float) (
                $input['tax_percent'] ?? 0
            );

            $baseAmount = round(
                $quantity * $rate,
                2
            );

            $taxAmount = round(
                $baseAmount * $taxPercent / 100,
                2
            );

            $lineTotal = round(
                $baseAmount + $taxAmount,
                2
            );

            $subtotal += $baseAmount;
            $totalTax += $taxAmount;

            $items[] = [
                'item_id' => $input['item_id'] ?? null,
                'item_name' => $input['item_name'],
                'hsn_code' => $input['hsn_code'] ?? null,
                'unit' => $input['unit'] ?? 'PCS',
                'quantity' => $quantity,
                'rate' => $rate,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'total' => $lineTotal,
            ];
        }

        $subtotal = round($subtotal, 2);
        $totalTax = round($totalTax, 2);

        $chargesTotal = round(
            collect($data['additional_charges'] ?? [])
                ->sum(fn ($charge) =>
                    (float) $charge['amount']
                ),
            2
        );

        $beforeDiscount = round(
            $subtotal + $totalTax + $chargesTotal,
            2
        );

        $discountPercent = (float) (
            $data['discount_percent'] ?? 0
        );

        $discountAmount = $discountPercent > 0
            ? round(
                $beforeDiscount * $discountPercent / 100,
                2
            )
            : round(
                (float) ($data['discount_amount'] ?? 0),
                2
            );

        if ($discountAmount > $beforeDiscount) {
            throw ValidationException::withMessages([
                'discount_amount' =>
                    'Discount cannot exceed the total amount.',
            ]);
        }

        $roundOff = round(
            (float) ($data['round_off'] ?? 0),
            2
        );

        $grandTotal = round(
            $beforeDiscount - $discountAmount + $roundOff,
            2
        );

        if ($grandTotal < 0) {
            throw ValidationException::withMessages([
                'round_off' =>
                    'Total amount cannot be negative.',
            ]);
        }

        return [
            'header' => [
                'subtotal' => $subtotal,
                'total_tax' => $totalTax,
                'additional_charges_total' => $chargesTotal,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'round_off' => $roundOff,
                'grand_total' => $grandTotal,
            ],
            'items' => $items,
        ];
    }

    private function saveLines(
        ProformaInvoice $proforma,
        array $items,
        array $charges
    ): void {
        foreach ($items as $item) {
            $proforma->items()->create($item);
        }

        foreach ($charges as $charge) {
            $proforma->additionalCharges()->create([
                'name' => $charge['name'],
                'amount' => round(
                    (float) $charge['amount'],
                    2
                ),
            ]);
        }
    }

    private function findProforma(
        int $userId,
        int $id
    ) {
        return ProformaInvoice::where(
            'user_id',
            $userId
        )->with([
            'party',
            'items.item',
            'additionalCharges',
            'convertedInvoice',
        ])->findOrFail($id);
    }
}
