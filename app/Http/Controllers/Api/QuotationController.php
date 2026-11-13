<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuotationController extends Controller
{
    // GET /api/quotations/next-number
    public function nextNumber(Request $request)
    {
        $userId = $request->user()->id;

        $lastId = Quotation::where('user_id', $userId)
            ->max('id');

        return response()->json([
            'success' => true,
            'next_number' => 'QT-' . (($lastId ?? 0) + 1),
        ]);
    }

    // GET /api/quotations
    public function index(Request $request)
    {
        $query = Quotation::where(
            'user_id',
            $request->user()->id
        )->with('party');

        if ($request->filled('search')) {
            $search = $request->query('search');

            $query->where(function ($q) use ($search) {
                $q->where(
                    'quotation_number',
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
                ->orderByDesc('quotation_date')
                ->orderByDesc('id')
                ->paginate(20),
        ]);
    }

    // GET /api/quotations/{id}
    public function show(Request $request, int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->findQuotation(
                $request->user()->id,
                $id
            ),
        ]);
    }

    // POST /api/quotations
    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $data = $this->validateData($request, $userId);

        $quotation = DB::transaction(function () use (
            $userId,
            $data
        ) {
            $totals = $this->calculateTotals($data);

            // Temporary unique number; replaced after ID is assigned.
            $quotation = Quotation::create([
                'user_id' => $userId,
                'party_id' => $data['party_id'],
                'quotation_number' => 'TMP-' . Str::uuid(),
                'quotation_date' => $data['quotation_date'],
                'expiry_date' => $data['expiry_date'],
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                ...$totals['header'],
            ]);

            $quotation->update([
                'quotation_number' => 'QT-' . $quotation->id,
            ]);

            $this->saveLines(
                $quotation,
                $totals['items'],
                $data['additional_charges'] ?? []
            );

            return $quotation;
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation created successfully',
            'data' => $this->findQuotation(
                $userId,
                $quotation->id
            ),
        ], 201);
    }

    // PUT /api/quotations/{id}
    public function update(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $data = $this->validateData($request, $userId);

        DB::transaction(function () use ($userId, $id, $data) {
            $quotation = Quotation::where('user_id', $userId)
                ->lockForUpdate()
                ->findOrFail($id);

            if ($quotation->converted_invoice_id !== null) {
                throw ValidationException::withMessages([
                    'quotation' =>
                        'Converted quotations cannot be edited.',
                ]);
            }

            $totals = $this->calculateTotals($data);

            $quotation->update([
                'party_id' => $data['party_id'],
                'quotation_date' => $data['quotation_date'],
                'expiry_date' => $data['expiry_date'],
                'notes' => $data['notes'] ?? null,
                ...$totals['header'],
            ]);

            $quotation->items()->delete();
            $quotation->additionalCharges()->delete();

            $this->saveLines(
                $quotation,
                $totals['items'],
                $data['additional_charges'] ?? []
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation updated successfully',
            'data' => $this->findQuotation($userId, $id),
        ]);
    }

    // DELETE /api/quotations/{id}
    public function destroy(Request $request, int $id)
    {
        $userId = $request->user()->id;

        DB::transaction(function () use ($userId, $id) {
            $quotation = Quotation::where('user_id', $userId)
                ->lockForUpdate()
                ->findOrFail($id);

            if ($quotation->converted_invoice_id !== null) {
                throw ValidationException::withMessages([
                    'quotation' =>
                        'Converted quotations cannot be deleted.',
                ]);
            }

            $quotation->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation deleted successfully',
        ]);
    }

    private function validateData(Request $request, int $userId)
    {
        return $request->validate([
            'party_id' => [
                'required',
                'integer',
                Rule::exists('parties', 'id')
                    ->where('user_id', $userId),
            ],
            'quotation_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:quotation_date',
            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.item_id' => [
                'nullable',
                'integer',
                Rule::exists('items', 'id')
                    ->where('user_id', $userId),
            ],
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.unit' => 'nullable|string|max:30',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.gst_percent' =>
                'nullable|numeric|between:0,100',

            'additional_charges' => 'nullable|array',
            'additional_charges.*.name' =>
                'required|string|max:255',
            'additional_charges.*.amount' =>
                'required|numeric|min:0',

            'discount_percent' =>
                'nullable|numeric|between:0,100',
            'discount_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
        ]);
    }

    private function calculateTotals(array $data): array
    {
        $subtotal = 0;
        $totalTax = 0;
        $items = [];

        foreach ($data['items'] as $index => $input) {
            $qty = round((float) $input['qty'], 3);
            $price = round((float) $input['price'], 2);
            $discount = round(
                (float) ($input['discount'] ?? 0),
                2
            );
            $gstPercent = (float) (
                $input['gst_percent'] ?? 0
            );

            $gross = round($qty * $price, 2);

            if ($discount > $gross) {
                throw ValidationException::withMessages([
                    "items.$index.discount" =>
                        'Item discount exceeds item value.',
                ]);
            }

            $taxable = round($gross - $discount, 2);
            $gstAmount = round(
                $taxable * $gstPercent / 100,
                2
            );
            $lineTotal = round(
                $taxable + $gstAmount,
                2
            );

            $subtotal += $taxable;
            $totalTax += $gstAmount;

            $items[] = [
                'item_id' => $input['item_id'] ?? null,
                'description' => $input['description'],
                'qty' => $qty,
                'unit' => $input['unit'] ?? null,
                'price' => $price,
                'discount' => $discount,
                'gst_percent' => $gstPercent,
                'gst_amount' => $gstAmount,
                'line_total' => $lineTotal,
            ];
        }

        $subtotal = round($subtotal, 2);
        $totalTax = round($totalTax, 2);

        $chargesTotal = round(
            collect($data['additional_charges'] ?? [])
                ->sum(fn ($c) => (float) $c['amount']),
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
                    'Discount cannot exceed quotation total.',
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
                    'Quotation total cannot be negative.',
            ]);
        }

        return [
            'header' => [
                'subtotal' => $subtotal,
                'total_tax' => $totalTax,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'round_off' => $roundOff,
                'grand_total' => $grandTotal,
            ],
            'items' => $items,
        ];
    }

    private function saveLines(
        Quotation $quotation,
        array $items,
        array $charges
    ): void {
        foreach ($items as $item) {
            $quotation->items()->create($item);
        }

        foreach ($charges as $charge) {
            $quotation->additionalCharges()->create([
                'name' => $charge['name'],
                'amount' => round(
                    (float) $charge['amount'],
                    2
                ),
            ]);
        }
    }

    private function findQuotation(int $userId, int $id)
    {
        return Quotation::where('user_id', $userId)
            ->with([
                'party',
                'items.item',
                'additionalCharges',
                'convertedInvoice',
            ])
            ->findOrFail($id);
    }
}
