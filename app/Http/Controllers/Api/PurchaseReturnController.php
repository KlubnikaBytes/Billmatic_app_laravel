<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessDetail;
use App\Models\Item;
use App\Models\Party;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnAdditionalCharge;
use App\Models\PurchaseReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseReturnController extends Controller
{
    /** Return the latest numeric suffix; Flutter uses it to suggest the next number. */
    public function lastNumber(Request $request)
    {
        $user = $request->user();

        $latest = PurchaseReturn::where('user_id', $user->id)
            ->orderByDesc('id')
            ->value('purchase_return_number');

        preg_match('/(\d+)$/', (string) $latest, $match);

        return response()->json([
            'success' => true,
            'last_number' => (int) ($match[1] ?? 0),
        ]);
    }

    /** Secure source-purchase detail endpoint used by the Flutter item picker. */
    public function sourcePurchase(Request $request, $id)
    {
        $purchase = Purchase::where('user_id', $request->user()->id)
            ->with(['items.item'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $purchase->id,
                'purchase_number' => $purchase->purchase_number,
                'purchase_date' => $purchase->purchase_date,
                'party_id' => $purchase->party_id,
                'items' => $purchase->items->map(function ($row) {
                    return [
                        'item_id' => $row->item_id,
                        'description' => $row->description ?? $row->item->name ?? '',
                        'hsn' => $row->item->hsn_code ?? '',
                        'qty' => (float) $row->qty,
                        'unit' => $row->unit ?? $row->item->unit ?? 'PCS',
                        'price' => (float) $row->price,
                        'discount' => (float) ($row->discount ?? 0),
                        'gst_percent' => (float) $row->gst_percent,
                        'gst_amount' => (float) $row->gst_amount,
                        'line_total' => (float) $row->line_total,
                    ];
                })->values(),
            ],
        ]);
    }

    /** Create a purchase return, reverse stock, and return preview data. */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'purchase_return_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('purchase_returns', 'purchase_return_number')
                    ->where(fn ($query) => $query->where('user_id', $user->id)),
            ],
            'return_date' => 'required|date',
            'party_id' => [
                'required',
                'integer',
                Rule::exists('parties', 'id')->where(
                    fn ($query) => $query->where('user_id', $user->id)
                ),
            ],
            'purchase_id' => [
                'nullable',
                'integer',
                Rule::exists('purchases', 'id')->where(
                    fn ($query) => $query->where('user_id', $user->id)
                ),
            ],
            'place_of_supply' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'received_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:50',
            'additional_charges' => 'nullable|array',
            'additional_charges.*.name' => 'required|string|max:255',
            'additional_charges.*.amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
        ]);

        $purchaseReturn = DB::transaction(function () use ($data, $user) {
            $purchase = null;
            if (!empty($data['purchase_id'])) {
                $purchase = Purchase::where('user_id', $user->id)
                    ->where('party_id', $data['party_id'])
                    ->lockForUpdate()
                    ->findOrFail($data['purchase_id']);
            }

            $party = Party::where('user_id', $user->id)
                ->findOrFail($data['party_id']);

            $preparedItems = [];
            $lockedItems = [];
            $returnedQtyInThisRequest = [];
            $subtotal = 0.0;
            $totalTax = 0.0;

            foreach ($data['items'] as $index => $line) {
                $item = Item::where('user_id', $user->id)
                    ->lockForUpdate()
                    ->findOrFail($line['item_id']);
                $lockedItems[$item->id] = $item;

                $sourcePurchaseItem = null;
                $purchaseQty = 0.0;
                $alreadyReturned = 0.0;

                // When an invoice is linked, validate against its original lines.
                // Without one, the return can still be created from the item picker.
                if ($purchase !== null) {
                    $sourcePurchaseItem = PurchaseItem::where('purchase_id', $purchase->id)
                        ->where('item_id', $item->id)
                        ->lockForUpdate()
                        ->first();
                    $purchaseQty = (float) PurchaseItem::where('purchase_id', $purchase->id)
                        ->where('item_id', $item->id)
                        ->sum('qty');

                    if ($purchaseQty <= 0 || $sourcePurchaseItem === null) {
                        throw ValidationException::withMessages([
                            "items.$index.item_id" => [
                                "{$item->name} is not on the selected purchase invoice.",
                            ],
                        ]);
                    }

                    $alreadyReturned = (float) PurchaseReturnItem::where('item_id', $item->id)
                        ->whereHas('purchaseReturn', function ($query) use ($purchase) {
                            $query->where('purchase_id', $purchase->id);
                        })
                        ->sum('qty');
                }

                $qty = (float) $line['qty'];
                $requestedSoFar = (float) ($returnedQtyInThisRequest[$item->id] ?? 0);

                if ($purchase !== null &&
                    $alreadyReturned + $requestedSoFar + $qty > $purchaseQty + 0.0001) {
                    throw ValidationException::withMessages([
                        "items.$index.qty" => [
                            "Return quantity for {$item->name} exceeds the quantity on the purchase invoice.",
                        ],
                    ]);
                }

                // Do not allow a return to make tracked stock negative.
                $currentStock = (float) ($item->opening_stock ?? 0);
                if ($requestedSoFar + $qty > $currentStock + 0.0001) {
                    throw ValidationException::withMessages([
                        "items.$index.qty" => [
                            "There is not enough current stock of {$item->name} to return.",
                        ],
                    ]);
                }

                if ($sourcePurchaseItem !== null) {
                    // A linked return uses the original purchase-line rate and tax.
                    $sourceLineQty = (float) $sourcePurchaseItem->qty;
                    $price = (float) $sourcePurchaseItem->price;
                    $discountPerUnit = $sourceLineQty > 0
                        ? (float) ($sourcePurchaseItem->discount ?? 0) / $sourceLineQty
                        : 0;
                    $discount = round($discountPerUnit * $qty, 2);
                    $gstPercent = (float) ($sourcePurchaseItem->gst_percent ?? 0);
                } else {
                    // If no source invoice is linked, use the selected item's
                    // rate, discount, and GST sent by the existing item picker.
                    $price = (float) $line['price'];
                    $discount = (float) ($line['discount'] ?? 0);
                    $gstPercent = (float) ($line['gst_percent'] ?? 0);
                }
                $taxable = max(0, ($qty * $price) - $discount);
                $gstAmount = round($taxable * ($gstPercent / 100), 2);
                $lineTotal = round($taxable + $gstAmount, 2);

                $subtotal += $taxable;
                $totalTax += $gstAmount;
                $returnedQtyInThisRequest[$item->id] = $requestedSoFar + $qty;

                $preparedItems[] = [
                    'attributes' => [
                        'item_id' => $item->id,
                        'description' => $sourcePurchaseItem?->description
                            ?? ($line['description'] ?? $item->name),
                        'hsn' => $item->hsn_code ?? null,
                        'qty' => $qty,
                        'unit' => $sourcePurchaseItem?->unit
                            ?? ($line['unit'] ?? $item->unit ?? 'PCS'),
                        'price' => $price,
                        'discount' => $discount,
                        'gst_percent' => $gstPercent,
                        'gst_amount' => $gstAmount,
                        'line_total' => $lineTotal,
                    ],
                ];
            }

            $additionalCharges = $data['additional_charges'] ?? [];
            $additionalTotal = collect($additionalCharges)->sum('amount');
            $discountPercent = (float) ($data['discount_percent'] ?? 0);
            $discountAmount = (float) ($data['discount_amount'] ?? 0);

            if ($discountPercent > 0) {
                $discountAmount = (($subtotal + $totalTax + $additionalTotal)
                    * $discountPercent) / 100;
            }
            $discountAmount = round($discountAmount, 2);

            $roundOff = (float) ($data['round_off'] ?? 0);
            $grandTotal = round(
                $subtotal + $totalTax + $additionalTotal - $discountAmount + $roundOff,
                2
            );

            if ($grandTotal < 0) {
                throw ValidationException::withMessages([
                    'discount_amount' => ['The return total cannot be less than zero.'],
                ]);
            }

            $receivedAmount = round((float) ($data['received_amount'] ?? 0), 2);
            if ($receivedAmount > $grandTotal + 0.0001) {
                throw ValidationException::withMessages([
                    'received_amount' => ['Amount received cannot exceed the return total.'],
                ]);
            }

            $balanceAmount = max(0, round($grandTotal - $receivedAmount, 2));
            $status = $balanceAmount <= 0.01
                ? 'paid'
                : ($receivedAmount <= 0 ? 'unpaid' : 'partial');

            $return = PurchaseReturn::create([
                'user_id' => $user->id,
                'party_id' => $party->id,
                'purchase_id' => $purchase?->id,
                'purchase_return_number' => $data['purchase_return_number'],
                'return_date' => $data['return_date'],
                'place_of_supply' => $data['place_of_supply']
                    ?? $purchase?->place_of_supply,
                'subtotal' => round($subtotal, 2),
                'total_tax' => round($totalTax, 2),
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'round_off' => $roundOff,
                'grand_total' => $grandTotal,
                'received_amount' => $receivedAmount,
                'balance_amount' => $balanceAmount,
                'payment_mode' => $data['payment_mode'] ?? null,
                'status' => $status,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($preparedItems as $prepared) {
                $return->items()->create($prepared['attributes']);
            }

            // Apply one stock deduction per item (a bill can repeat an item).
            foreach ($returnedQtyInThisRequest as $itemId => $totalQty) {
                $item = $lockedItems[$itemId];
                $item->opening_stock = round(
                    (float) $item->opening_stock - (float) $totalQty,
                    3
                );
                $item->stock_value = round(
                    (float) $item->opening_stock * (float) ($item->purchase_price ?? 0),
                    2
                );
                $item->save();
            }

            foreach ($additionalCharges as $charge) {
                $return->additionalCharges()->create([
                    'name' => $charge['name'],
                    'amount' => $charge['amount'],
                ]);
            }

            return $return->fresh();
        });

        return response()->json([
            'success' => true,
            'message' => 'Purchase return created successfully.',
            'data' => $this->previewData($purchaseReturn),
        ], 201);
    }

    /** Fetch one saved return for reopening its invoice preview. */
    public function show(Request $request, $id)
    {
        $return = PurchaseReturn::where('user_id', $request->user()->id)
            ->with(['purchase', 'party', 'items.item', 'additionalCharges'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->previewData($return),
        ]);
    }

    private function previewData(PurchaseReturn $return): array
    {
        $return->loadMissing([
            'user',
            'purchase',
            'party',
            'items.item',
            'additionalCharges',
        ]);

        $business = BusinessDetail::where('user_id', $return->user_id)->first();

        return [
            'id' => $return->id,
            'purchase_return_number' => $return->purchase_return_number,
            'return_date' => $return->return_date?->format('Y-m-d'),
            'purchase_id' => $return->purchase_id,
            'purchase_number' => $return->purchase?->purchase_number ?? '',
            'purchase_date' => $return->purchase?->purchase_date,
            'place_of_supply' => $return->place_of_supply ?? '',
            'status' => $return->status,
            'subtotal' => (float) $return->subtotal,
            'total_tax' => (float) $return->total_tax,
            'additional_charge_total' => (float) $return->additionalCharges->sum('amount'),
            'discount_percent' => (float) $return->discount_percent,
            'discount_amount' => (float) $return->discount_amount,
            'round_off' => (float) $return->round_off,
            'grand_total' => (float) $return->grand_total,
            'received_amount' => (float) $return->received_amount,
            'balance_amount' => (float) $return->balance_amount,
            'payment_mode' => $return->payment_mode,
            'notes' => $return->notes,
            'business' => [
                'name' => $business->business_name ?? '',
                'gstin' => $business->gstin ?? '',
                'address' => $business->address ?? '',
                'mobile' => $return->user->mobile ?? '',
            ],
            'party' => [
                'id' => $return->party->id ?? null,
                'party_name' => $return->party->party_name ?? '',
                'contact_number' => $return->party->contact_number ?? '',
                'gst_number' => $return->party->gst_number ?? '',
            ],
            'items' => $return->items->map(function ($row) {
                return [
                    'item_id' => $row->item_id,
                    'description' => $row->description ?? $row->item->name ?? '',
                    'hsn' => $row->hsn ?? $row->item->hsn_code ?? '',
                    'qty' => (float) $row->qty,
                    'unit' => $row->unit ?? '',
                    'price' => (float) $row->price,
                    'discount' => (float) $row->discount,
                    'gst_percent' => (float) $row->gst_percent,
                    'gst_amount' => (float) $row->gst_amount,
                    'line_total' => (float) $row->line_total,
                ];
            })->values()->all(),
            'additional_charges' => $return->additionalCharges->map(function ($charge) {
                return [
                    'name' => $charge->name,
                    'amount' => (float) $charge->amount,
                ];
            })->values()->all(),
        ];
    }
}
