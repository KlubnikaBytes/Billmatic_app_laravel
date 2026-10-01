<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Item;
use App\Models\Party;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseAdditionalCharge;

class AutoPurchaseController extends Controller
{
    public function storeAuto(Request $request)
    {
        $user = $request->user();

        // $data = $request->validate([
        //     'party_name' => 'required|string',
        //     'place_of_supply' => 'nullable|string',
        //     'items' => 'required|array|min:1',

        //     'items.*.description' => 'required|string',
        //     'items.*.qty' => 'required|numeric',
        //     'items.*.price' => 'required|numeric',
        //     'items.*.gst_percent' => 'nullable|numeric',
        //     'items.*.hsn' => 'nullable|string',
        // ]);

        $data = $request->validate([
    'party_name' => 'required|string',
    'place_of_supply' => 'nullable|string',

    'items' => 'required|array|min:1',

    'items.*.description' => 'required|string',
    'items.*.qty' => 'required|numeric',
    'items.*.price' => 'required|numeric',
    'items.*.gst_percent' => 'nullable|numeric',
    'items.*.hsn' => 'nullable|string',

    // =====================================================
    // OCR TOTALS / ADJUSTMENTS
    // =====================================================

    'subtotal' => 'nullable|numeric',
    'total_tax' => 'nullable|numeric',

    'additional_charge_total' => 'nullable|numeric',

    'discount_amount' => 'nullable|numeric',
    'discount_percent' => 'nullable|numeric',

    'round_off' => 'nullable|numeric',

    'tcs_amount' => 'nullable|numeric',

    'grand_total' => 'nullable|numeric',

    'received_amount' => 'nullable|numeric',
    'balance_amount' => 'nullable|numeric',
]);

        return DB::transaction(function () use ($data, $user) {

            // ================= PARTY =================
            $isNewParty = false;

            $party = Party::where('user_id', $user->id)
                ->where('party_name', $data['party_name'])
                ->first();

            if (!$party) {
                $isNewParty = true;

                $party = Party::create([
                    'user_id' => $user->id,
                    'party_name' => $data['party_name'],
                    'party_type' => 'supplier',
                    'opening_balance_type' => 'pay',
                ]);
            }

            $subtotal = 0;
            $totalTax = 0;
            $preparedItems = [];

            foreach ($data['items'] as $line) {

                // ================= ITEM =================
                $isNewItem = false;

                $item = Item::where('user_id', $user->id)
                    ->where('name', $line['description'])
                    ->where('hsn_code', $line['hsn'] ?? '')
                    ->first();

                if (!$item) {
                    $isNewItem = true;

                    $item = Item::create([
                        'user_id' => $user->id,
                        'name' => $line['description'],
                        'hsn_code' => $line['hsn'] ?? '',
                        'purchase_price' => $line['price'],
                        'opening_stock' => 0,
                        'item_type' => 'product',
                    ]);
                }

                // ✅ STOCK INCREASE
                $item->opening_stock += $line['qty'];
                $item->save();

                // ================= CALC =================
                $qty = $line['qty'];
                $price = $line['price'];
                $gst = $line['gst_percent'] ?? 0;

                $lineAmount = $qty * $price;
                $gstAmount = $lineAmount * ($gst / 100);

                $subtotal += $lineAmount;
                $totalTax += $gstAmount;

                $preparedItems[] = [
                    'item_id' => $item->id,
                    'description' => $line['description'],
                    'hsn' => $line['hsn'] ?? '', // ✅ ADD THIS
                    'qty' => $qty,
                    'price' => $price,
                    'gst_percent' => $gst,
                    'gst_amount' => $gstAmount,
                    'line_total' => $lineAmount + $gstAmount,
                    'is_new' => $isNewItem, // ✅ IMPORTANT
                ];
            }

            // $grandTotal = $subtotal + $totalTax;

            // =====================================================
// ADDITIONAL CHARGES
// =====================================================

$additionalTotal = (float) (
    $data['additional_charge_total'] ?? 0
);


// =====================================================
// DISCOUNT
// =====================================================

$discountAmount = (float) (
    $data['discount_amount'] ?? 0
);


// =====================================================
// ROUND OFF
// =====================================================

$roundOff = (float) (
    $data['round_off'] ?? 0
);


// =====================================================
// TCS
// =====================================================

$tcsAmount = (float) (
    $data['tcs_amount'] ?? 0
);


// =====================================================
// GRAND TOTAL
// =====================================================

// =====================================================
// CALCULATED GRAND TOTAL
// =====================================================

$calculatedGrandTotal =
    $subtotal
    + $totalTax
    + $additionalTotal
    - $discountAmount
    + $roundOff
    + $tcsAmount;

$calculatedGrandTotal =
    round(
        $calculatedGrandTotal,
        2
    );

// =====================================================
// OCR GRAND TOTAL
// =====================================================

$ocrGrandTotal = null;

if (
    isset($data['grand_total']) &&
    $data['grand_total'] !== null
) {

    $ocrGrandTotal =
        round(
            (float)
            $data['grand_total'],
            2
        );
}

// =====================================================
// GRAND TOTAL DECISION
// =====================================================

if (
    $ocrGrandTotal !== null &&
    abs(
        $ocrGrandTotal -
        $calculatedGrandTotal
    ) <= 1.00
) {

    /*
     * OCR total and calculated total agree.
     * Use OCR total to preserve invoice rounding.
     */

    $grandTotal =
        $ocrGrandTotal;

} else {

    /*
     * Calculated total is the trusted value
     * when OCR total and line calculation
     * disagree substantially.
     */

    $grandTotal =
        $calculatedGrandTotal;
}

$grandTotal =
    round(
        $grandTotal,
        2
    );

           
            // ================= PURCHASE NUMBER =================
        $lastPurchase = Purchase::where('user_id', $user->id)
       ->where('purchase_number', 'LIKE', 'AUTO-%')
       ->latest('id')
       ->lockForUpdate()
       ->first();

      $nextNumber = 1;

       if (
       $lastPurchase &&
       !empty($lastPurchase->purchase_number) &&
        preg_match('/AUTO-(\d+)/', $lastPurchase->purchase_number, $match)
        ) {
        $nextNumber = ((int) $match[1]) + 1;
        }

        $purchaseNumber = 'AUTO-' . $nextNumber;

            // ================= CREATE PURCHASE =================
            // $purchase = Purchase::create([
            //     'user_id' => $user->id,
            //     // 'purchase_number' => 'AUTO-' . time(),
            //     'purchase_number' => $purchaseNumber,
            //     'purchase_date' => now(),
            //     'party_id' => $party->id,
            //     'place_of_supply' => $data['place_of_supply'],
            //     'subtotal' => $subtotal,
            //     'total_tax' => $totalTax,
            //     'grand_total' => $grandTotal,
            //     'status' => 'paid',
            // ]);

            // =====================================================
// PAYMENT
// =====================================================

$receivedAmount = (float) (
    $data['received_amount'] ?? 0
);

// =====================================================
// PROTECT PAYMENT
// =====================================================

if (
    $receivedAmount < 0
) {
    $receivedAmount = 0;
}

if (
    $receivedAmount > $grandTotal
) {
    $receivedAmount =
        $grandTotal;
}

$receivedAmount =
    round(
        $receivedAmount,
        2
    );

$balanceAmount =
    round(
        $grandTotal -
        $receivedAmount,
        2
    );

if (
    abs($balanceAmount) < 0.01
) {
    $balanceAmount = 0;
}
if (abs($balanceAmount) < 0.01) {
    $balanceAmount = 0;
}


// =====================================================
// STATUS
// =====================================================

if ($balanceAmount <= 0) {

    $status = 'paid';

} elseif ($receivedAmount <= 0) {

    $status = 'unpaid';

} else {

    $status = 'partial';
}


// =====================================================
// CREATE PURCHASE
// =====================================================

$purchase = Purchase::create([

    'user_id' =>
        $user->id,

    'purchase_number' =>
        $purchaseNumber,

    'purchase_date' =>
        now(),

    'party_id' =>
        $party->id,

    'place_of_supply' =>
        $data['place_of_supply'] ?? null,

    'subtotal' =>
        $subtotal,

    'total_tax' =>
        $totalTax,

    'discount_percent' =>
        $data['discount_percent'] ?? 0,

    'discount_amount' =>
        $discountAmount,

    'round_off' =>
        $roundOff,

    'tcs_amount' =>
        $tcsAmount,

    'received_amount' =>
        $receivedAmount,

    'balance_amount' =>
        $balanceAmount,

    'grand_total' =>
        $grandTotal,

    'status' =>
        $status,
]);

// =====================================================
// SAVE ADDITIONAL CHARGE
// =====================================================

if ($additionalTotal > 0) {

    PurchaseAdditionalCharge::create([

        'purchase_id' =>
            $purchase->id,

        'name' =>
            'Additional Charges',

        'amount' =>
            $additionalTotal,
    ]);
}

            foreach ($preparedItems as $line) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $line['item_id'],
                    'description' => $line['description'],
                    'hsn' => $line['hsn'] ?? '', // ✅ ADD
                    'qty' => $line['qty'],
                    'price' => $line['price'],
                    'gst_percent' => $line['gst_percent'],
                    'gst_amount' => $line['gst_amount'],
                    'line_total' => $line['line_total'],
                ]);
            }

            // ================= FINAL RESPONSE =================
            // return response()->json([
            //     'success' => true,
            //     'data' => [
            //         'purchase' => $purchase->load(['items', 'party']),
            //         'id' => $purchase->id,
            //         'purchase_number' => $purchase->purchase_number,
            //         'purchase_date' => $purchase->purchase_date,
            //         'place_of_supply' => $purchase->place_of_supply,
            //         'subtotal' => $purchase->subtotal,
            //         'total_tax' => $purchase->total_tax,
            //         'grand_total' => $purchase->grand_total,
            //         'status' => $purchase->status,

            //         'party' => [
            //             'id' => $party->id,
            //             'party_name' => $party->party_name,
            //             'is_new' => $isNewParty, // ✅ PARTY FLAG
            //             'contact_number' => $party->contact_number ?? '', // ✅
            //         ],

            //         'items' => $preparedItems, // ✅ ITEMS WITH FLAG
            //     ]
            // ], 201);
            return response()->json([

    'success' => true,

    'data' => [

        'purchase' =>
            $purchase->load([
                'items',
                'party',
                'additionalCharges',
            ]),

        'id' =>
            $purchase->id,

        'purchase_number' =>
            $purchase->purchase_number,

        'purchase_date' =>
            $purchase->purchase_date,

        'place_of_supply' =>
            $purchase->place_of_supply,

        'subtotal' =>
            (float) $purchase->subtotal,

        'total_tax' =>
            (float) $purchase->total_tax,

        'additional_charge_total' =>
            (float) $additionalTotal,

        'discount_amount' =>
            (float) $purchase->discount_amount,

        'round_off' =>
            (float) $purchase->round_off,

        'tcs_amount' =>
            (float) $purchase->tcs_amount,

        'grand_total' =>
            (float) $purchase->grand_total,

        'received_amount' =>
            (float) $purchase->received_amount,

        'balance_amount' =>
            (float) $purchase->balance_amount,

        'status' =>
            $purchase->status,

        'party' => [

            'id' =>
                $party->id,

            'party_name' =>
                $party->party_name,

            'is_new' =>
                $isNewParty,

            'contact_number' =>
                $party->contact_number ?? '',
        ],

        'items' =>
            $preparedItems,

        'additional_charges' =>
            $purchase->additionalCharges,
    ]

], 201);
        });
    }
}