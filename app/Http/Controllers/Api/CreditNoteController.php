<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\CreditNoteAdditionalCharge;
use App\Models\SalesReturnItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreditNoteController extends Controller
{
    // GET /api/credit-notes/next-number
    public function nextNumber(Request $request)
    {
        $nextId = (CreditNote::max('id') ?? 0) + 1;

        return response()->json([
            'success' => true,
            'next_number' => 'CN-' . $nextId,
        ]);
    }

    // GET /api/credit-notes/invoices?search=
    public function invoices(Request $request)
    {
        $userId = $request->user()->id;
        $search = trim((string) $request->query('search', ''));

        $invoices = Invoice::where('user_id', $userId)
            ->with('party')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        'invoice_number',
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
            })
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    // GET /api/credit-notes/invoices/{id}
    public function invoiceDetails(Request $request, int $id)
    {
        $userId = $request->user()->id;

        $invoice = Invoice::where('user_id', $userId)
            ->with(['party', 'items.item'])
            ->findOrFail($id);

        $items = $invoice->items->map(function ($line) {
            $creditQty = (float) CreditNoteItem::where(
                'invoice_item_id',
                $line->id
            )->sum('qty');

            $returnQty = (float) SalesReturnItem::where(
                'invoice_item_id',
                $line->id
            )->sum('qty');

            $available = max(
                0,
                round(
                    (float) $line->qty
                    - $creditQty
                    - $returnQty,
                    3
                )
            );

            return [
                'invoice_item_id' => $line->id,
                'item_id' => $line->item_id,
                'item_name' => $line->item->name ?? '',
                'description' => $line->description,
                'qty' => (float) $line->qty,
                'returned_qty' => $creditQty + $returnQty,
                'available_qty' => $available,
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
                'items' => $items,
            ],
        ]);
    }

    // POST /api/credit-notes
    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'invoice_id' => [
                'nullable',
                'integer',
                Rule::exists('invoices', 'id')
                    ->where('user_id', $userId),
            ],
            'party_id' => [
                'required',
                'integer',
                Rule::exists('parties', 'id')
                    ->where('user_id', $userId),
            ],
            'credit_note_date' => 'required|date',
            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.invoice_item_id' => 'nullable|integer|distinct',
            'items.*.item_id' => [
                'required',
                'integer',
                Rule::exists('items', 'id')
                    ->where('user_id', $userId),
            ],
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|between:0,100',
            'items.*.description' => 'nullable|string',
            'items.*.unit' => 'nullable|string|max:50',

            'additional_charges' => 'nullable|array',
            'additional_charges.*.name' =>
                'required|string|max:255',
            'additional_charges.*.amount' =>
                'required|numeric|min:0',

            'discount_percent' =>
                'nullable|numeric|between:0,100',
            'discount_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'tcs_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',
        ]);

        $creditNote = DB::transaction(
            function () use ($data, $userId) {
                $invoice = null;

                if (!empty($data['invoice_id'])) {
                    $invoice = Invoice::where('user_id', $userId)
                        ->lockForUpdate()
                        ->findOrFail($data['invoice_id']);

                    if (
                        (int) $invoice->party_id !==
                        (int) $data['party_id']
                    ) {
                        throw ValidationException::withMessages([
                            'party_id' =>
                                'Party must match the selected invoice.',
                        ]);
                    }
                }

                Party::where('user_id', $userId)
                    ->findOrFail($data['party_id']);

                $subtotal = 0;
                $totalTax = 0;
                $preparedItems = [];

                foreach ($data['items'] as $index => $input) {
                    $qty = round((float) $input['qty'], 3);

                    if ($qty <= 0) {
                        throw ValidationException::withMessages([
                            "items.$index.qty" =>
                                'Quantity must be greater than zero.',
                        ]);
                    }

                    $item = Item::where('user_id', $userId)
                        ->findOrFail($input['item_id']);

                    $invoiceItemId = null;

                    if ($invoice) {
                        if (empty($input['invoice_item_id'])) {
                            throw ValidationException::withMessages([
                                "items.$index.invoice_item_id" =>
                                    'Select an item from the invoice.',
                            ]);
                        }

                        $line = InvoiceItem::where(
                            'invoice_id',
                            $invoice->id
                        )
                            ->lockForUpdate()
                            ->findOrFail(
                                $input['invoice_item_id']
                            );

                        if (
                            (int) $line->item_id !==
                            (int) $item->id
                        ) {
                            throw ValidationException::withMessages([
                                "items.$index.item_id" =>
                                    'Item does not match invoice.',
                            ]);
                        }

                        $alreadyCredited = (float)
                            CreditNoteItem::where(
                                'invoice_item_id',
                                $line->id
                            )->sum('qty');

                        $alreadyReturned = (float)
                            SalesReturnItem::where(
                                'invoice_item_id',
                                $line->id
                            )->sum('qty');

                        $soldQty = (float) $line->qty;

                        $available = round(
                            $soldQty
                            - $alreadyCredited
                            - $alreadyReturned,
                            3
                        );

                        if ($soldQty <= 0 || $qty > $available + 0.00001) {
                            throw ValidationException::withMessages([
                                "items.$index.qty" =>
                                    "Only {$available} units are available for credit.",
                            ]);
                        }

                        $invoiceItemId = $line->id;
                        $price = (float) $line->price;
                        $gstPercent = (float) $line->gst_percent;

                        // Proportional original line discount.
                        $discount = round(
                            (float) $line->discount *
                            ($qty / $soldQty),
                            2
                        );

                        $unit = $line->unit;
                        $description = $line->description;
                    } else {
                        // Manual Credit Note: use form values.
                        $price = round(
                            (float) ($input['price'] ?? 0),
                            2
                        );

                        $gstPercent = (float) (
                            $input['gst_percent'] ?? 0
                        );

                        $discount = round(
                            (float) ($input['discount'] ?? 0),
                            2
                        );

                        $unit = $input['unit'] ?? null;
                        $description =
                            $input['description'] ?? null;
                    }

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
                        'line_total' => $lineTotal,
                    ];
                }

                $subtotal = round($subtotal, 2);
                $totalTax = round($totalTax, 2);

                $charges = $data['additional_charges'] ?? [];

                $chargesTotal = round(
                    collect($charges)->sum(
                        fn ($charge) =>
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
                        $beforeDiscount *
                        $discountPercent / 100,
                        2
                    )
                    : round(
                        (float) ($data['discount_amount'] ?? 0),
                        2
                    );

                if ($discountAmount > $beforeDiscount) {
                    throw ValidationException::withMessages([
                        'discount_amount' =>
                            'Discount exceeds the total amount.',
                    ]);
                }

                $roundOff = round(
                    (float) ($data['round_off'] ?? 0),
                    2
                );

                $tcsAmount = round(
                    (float) ($data['tcs_amount'] ?? 0),
                    2
                );

                $grandTotal = round(
                    $beforeDiscount
                    - $discountAmount
                    + $roundOff
                    + $tcsAmount,
                    2
                );

                if ($grandTotal < 0) {
                    throw ValidationException::withMessages([
                        'round_off' =>
                            'Credit Note total cannot be negative.',
                    ]);
                }

                $paidAmount = round(
                    (float) ($data['paid_amount'] ?? 0),
                    2
                );

                if ($paidAmount > $grandTotal) {
                    throw ValidationException::withMessages([
                        'paid_amount' =>
                            'Paid amount exceeds Credit Note total.',
                    ]);
                }

                $creditNote = CreditNote::create([
                    'user_id' => $userId,
                    'invoice_id' => $invoice?->id,
                    'party_id' => $data['party_id'],
                    'credit_note_number' =>
                        'PENDING-' . Str::uuid(),
                    'credit_note_date' => $data['credit_note_date'],
                    'subtotal' => $subtotal,
                    'total_tax' => $totalTax,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'round_off' => $roundOff,
                    'tcs_amount' => $tcsAmount,
                    'grand_total' => $grandTotal,
                    'paid_amount' => $paidAmount,
                    'balance_amount' => round(
                        $grandTotal - $paidAmount,
                        2
                    ),
                    'payment_mode' =>
                        $data['payment_mode'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                $creditNote->credit_note_number =
                    'CN-' . $creditNote->id;

                $creditNote->save();

                foreach ($preparedItems as $line) {
                    CreditNoteItem::create([
                        ...$line,
                        'credit_note_id' => $creditNote->id,
                    ]);

                    // Credit Note with returned goods:
                    // restore stock to existing item inventory.
                    $stockItem = Item::where('user_id', $userId)
                        ->lockForUpdate()
                        ->findOrFail($line['item_id']);

                    $stockItem->opening_stock = round(
                        (float) $stockItem->opening_stock
                        + $line['qty'],
                        3
                    );

                    $stockItem->save();
                }

                foreach ($charges as $charge) {
                    CreditNoteAdditionalCharge::create([
                        'credit_note_id' => $creditNote->id,
                        'name' => $charge['name'],
                        'amount' => round(
                            (float) $charge['amount'],
                            2
                        ),
                    ]);
                }

                return $creditNote;
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Credit Note created successfully',
            'data' => $this->details(
                $creditNote->id,
                $userId
            ),
        ], 201);
    }

    // GET /api/credit-notes
    public function index(Request $request)
    {
        $creditNotes = CreditNote::where(
            'user_id',
            $request->user()->id
        )
            ->with('party')
            ->orderByDesc('credit_note_date')
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $creditNotes,
        ]);
    }

    // GET /api/credit-notes/{id}
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
        return CreditNote::where('user_id', $userId)
            ->with([
                'party',
                'invoice',
                'items.item',
                'additionalCharges',
            ])
            ->findOrFail($id);
    }
}
