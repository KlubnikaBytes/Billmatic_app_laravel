<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Party;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\SalesReturnAdditionalCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesReturnController extends Controller
{
    // GET /api/sales-returns/next-number
    public function nextNumber(Request $request)
    {
        $userId = $request->user()->id;

        $nextId = (SalesReturn::max('id') ?? 0) + 1;

        return response()->json([
            'success' => true,
            'next_number' => 'SR-' . $nextId,
        ]);
    }

    // GET /api/sales-returns/invoices?search=
    public function invoices(Request $request)
    {
        $userId = $request->user()->id;
        $search = trim((string) $request->query('search', ''));

        $invoices = Invoice::where('user_id', $userId)
            ->with('party:id,party_name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('party', function ($partyQuery) use ($search) {
                            $partyQuery->where(
                                'party_name',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
            })
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    // GET /api/sales-returns/invoices/{id}
    public function invoiceDetails(Request $request, int $id)
    {
        $userId = $request->user()->id;

        $invoice = Invoice::where('user_id', $userId)
            ->with(['party', 'items.item'])
            ->findOrFail($id);

        $rows = $invoice->items->map(function ($line) {
            $alreadyReturned = (float) SalesReturnItem::where(
                'invoice_item_id',
                $line->id
            )->sum('qty');

            $soldQty = (float) $line->qty;

            return [
                'invoice_item_id' => $line->id,
                'item_id' => $line->item_id,
                'item_name' => $line->item->name ?? '',
                'description' => $line->description,
                'qty' => $soldQty,
                'returned_qty' => $alreadyReturned,
                'available_qty' => max(
                    0,
                    round($soldQty - $alreadyReturned, 3)
                ),
                'unit' => $line->unit,
                'price' => (float) $line->price,
                'discount' => (float) $line->discount,
                'gst_percent' => (float) $line->gst_percent,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date,
                'party_id' => $invoice->party_id,
                'party' => $invoice->party,
                'grand_total' => (float) $invoice->grand_total,
                'status' => $invoice->status,
                'items' => $rows,
            ],
        ]);
    }

    // POST /api/sales-returns
    // Supports invoice-linked and independent (manual) sales returns.
    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'invoice_id' => ['nullable', 'integer', Rule::exists('invoices', 'id')->where('user_id', $userId)],
            'party_id' => ['required', 'integer', Rule::exists('parties', 'id')->where('user_id', $userId)],
            'sales_return_date' => 'required|date',
            'notes' => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'tcs_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.invoice_item_id' => 'nullable|integer|distinct',
            'items.*.item_id' => ['required', 'integer', Rule::exists('items', 'id')->where('user_id', $userId)],
            'items.*.description' => 'nullable|string',
            'items.*.qty' => 'required|numeric|gt:0|decimal:0,3',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0|max:100',
            'additional_charges' => 'nullable|array',
            'additional_charges.*.name' => 'required|string|max:255',
            'additional_charges.*.amount' => 'required|numeric|min:0',
        ]);

        $salesReturn = DB::transaction(function () use ($data, $userId) {
            $invoiceId = $data['invoice_id'] ?? null;
            $invoice = null;

            if ($invoiceId !== null) {
                // Lock the invoice so simultaneous returns cannot exceed sold quantity.
                $invoice = Invoice::where('user_id', $userId)
                    ->lockForUpdate()->findOrFail($invoiceId);

                if ((int) $invoice->party_id !== (int) $data['party_id']) {
                    throw ValidationException::withMessages([
                        'party_id' => 'Selected party does not match the sales invoice.',
                    ]);
                }
            }

            Party::where('user_id', $userId)
                ->lockForUpdate()->findOrFail($data['party_id']);

            $subtotal = 0.0;
            $totalTax = 0.0;
            $preparedItems = [];
            $seenInvoiceItems = [];

            foreach ($data['items'] as $index => $input) {
                $qty = round((float) $input['qty'], 3);
                if ($qty <= 0) {
                    throw ValidationException::withMessages([
                        "items.$index.qty" => 'Return quantity must be greater than zero.',
                    ]);
                }

                $item = Item::where('user_id', $userId)
                    ->lockForUpdate()->findOrFail($input['item_id']);

                if ($invoice !== null) {
                    $invoiceItemId = $input['invoice_item_id'] ?? null;
                    if ($invoiceItemId === null) {
                        throw ValidationException::withMessages([
                            "items.$index.invoice_item_id" => 'Select an item from the original invoice.',
                        ]);
                    }
                    if (isset($seenInvoiceItems[$invoiceItemId])) {
                        throw ValidationException::withMessages([
                            "items.$index.invoice_item_id" => 'Duplicate invoice item.',
                        ]);
                    }
                    $seenInvoiceItems[$invoiceItemId] = true;

                    $original = InvoiceItem::where('invoice_id', $invoice->id)
                        ->lockForUpdate()->findOrFail($invoiceItemId);
                    if ((int) $original->item_id !== (int) $item->id) {
                        throw ValidationException::withMessages([
                            "items.$index.item_id" => 'Item does not match the selected invoice.',
                        ]);
                    }

                    $soldQty = (float) $original->qty;
                    $alreadyReturned = (float) SalesReturnItem::where(
                        'invoice_item_id', $original->id
                    )->sum('qty');
                    $available = round($soldQty - $alreadyReturned, 3);
                    if ($soldQty <= 0 || $qty > $available + 0.00001) {
                        throw ValidationException::withMessages([
                            "items.$index.qty" => "Only {$available} units can be returned.",
                        ]);
                    }

                    // Always use original invoice pricing for linked returns.
                    $price = (float) $original->price;
                    $discount = round((float) $original->discount * $qty / $soldQty, 2);
                    $gstPercent = (float) $original->gst_percent;
                    $description = $original->description;
                    $unit = $original->unit;
                    $invoiceItemId = $original->id;
                } else {
                    // Independent return: operator enters price and GST from item form.
                    // There is no original invoice to verify the sale or selling price.
                    if (!empty($input['invoice_item_id'])) {
                        throw ValidationException::withMessages([
                            "items.$index.invoice_item_id" => 'Do not send invoice item ID for an independent return.',
                        ]);
                    }
                    $price = round((float) $input['price'], 2);
                    $discount = round((float) ($input['discount'] ?? 0), 2);
                    $gstPercent = (float) ($input['gst_percent'] ?? 0);
                    $description = $input['description'] ?? null;
                    $unit = $input['unit'] ?? null;
                    $invoiceItemId = null;
                }

                $taxable = round($qty * $price - $discount, 2);
                if ($taxable < 0) {
                    throw ValidationException::withMessages([
                        "items.$index.discount" => 'Item discount exceeds item amount.',
                    ]);
                }
                $gstAmount = round($taxable * $gstPercent / 100, 2);
                $subtotal += $taxable;
                $totalTax += $gstAmount;

                $preparedItems[] = [
                    'invoice_item_id' => $invoiceItemId,
                    'item_id' => $item->id,
                    'description' => $description,
                    'qty' => $qty,
                    'unit' => $unit,
                    'price' => $price,
                    'discount' => $discount,
                    'gst_percent' => $gstPercent,
                    'gst_amount' => $gstAmount,
                    'line_total' => round($taxable + $gstAmount, 2),
                ];
            }

            $subtotal = round($subtotal, 2);
            $totalTax = round($totalTax, 2);
            $additionalTotal = round(collect($data['additional_charges'] ?? [])
                ->sum(fn ($charge) => (float) $charge['amount']), 2);
            $discountBase = round($subtotal + $totalTax + $additionalTotal, 2);
            $discountPercent = (float) ($data['discount_percent'] ?? 0);
            $discountAmount = $discountPercent > 0
                ? round($discountBase * $discountPercent / 100, 2)
                : round((float) ($data['discount_amount'] ?? 0), 2);

            if ($discountAmount > $discountBase) {
                throw ValidationException::withMessages([
                    'discount_amount' => 'Discount exceeds return amount.',
                ]);
            }
            $roundOff = round((float) ($data['round_off'] ?? 0), 2);
            $tcsAmount = round((float) ($data['tcs_amount'] ?? 0), 2);
            $grandTotal = round($discountBase - $discountAmount + $roundOff + $tcsAmount, 2);
            $paidAmount = round((float) ($data['paid_amount'] ?? 0), 2);
            if ($grandTotal < 0) {
                throw ValidationException::withMessages([
                    'round_off' => 'Sales Return total cannot be negative.',
                ]);
            }
            if ($paidAmount > $grandTotal) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Refund paid cannot exceed return total.',
                ]);
            }

            // Use a unique temporary number until the database ID is known.
            $salesReturn = SalesReturn::create([
                'user_id' => $userId,
                'invoice_id' => $invoice?->id,
                'party_id' => $data['party_id'],
                'sales_return_number' => 'PENDING-' . (string) \Illuminate\Support\Str::uuid(),
                'sales_return_date' => $data['sales_return_date'],
                'subtotal' => $subtotal,
                'total_tax' => $totalTax,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'round_off' => $roundOff,
                'tcs_amount' => $tcsAmount,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'balance_amount' => round($grandTotal - $paidAmount, 2),
                'payment_mode' => $data['payment_mode'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
            $salesReturn->sales_return_number = 'SR-' . $salesReturn->id;
            $salesReturn->save();

            foreach ($preparedItems as $line) {
                SalesReturnItem::create([
                    ...$line,
                    'sales_return_id' => $salesReturn->id,
                ]);

                // Only resellable returned goods should be added to stock.
                // This workflow assumes every returned item is resellable.
                $item = Item::where('user_id', $userId)
                    ->lockForUpdate()->findOrFail($line['item_id']);
                $item->opening_stock = round((float) $item->opening_stock + $line['qty'], 3);
                $item->save();
            }

            foreach ($data['additional_charges'] ?? [] as $charge) {
                SalesReturnAdditionalCharge::create([
                    'sales_return_id' => $salesReturn->id,
                    'name' => $charge['name'],
                    'amount' => round((float) $charge['amount'], 2),
                ]);
            }

            // Do not alter original invoice payments or party balance here:
            // a separate credit/refund allocation workflow is required.
            return $salesReturn;
        }, 3);

        return response()->json([
            'success' => true,
            'message' => 'Sales Return created successfully',
            'data' => $this->details($salesReturn->id, $userId),
        ], 201);
    }

    // GET /api/sales-returns
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $returns = SalesReturn::where('user_id', $userId)
            ->with('party:id,party_name')
            ->orderByDesc('sales_return_date')
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $returns,
        ]);
    }

    // GET /api/sales-returns/{id}
    public function show(Request $request, int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->details(
                $id,
                $request->user()->id
            ),
        ]);
    }

    private function details(int $id, int $userId)
    {
        return SalesReturn::where('user_id', $userId)
            ->with([
                'party',
                'invoice',
                'items.item',
                'additionalCharges',
            ])
            ->findOrFail($id);
    }
}
