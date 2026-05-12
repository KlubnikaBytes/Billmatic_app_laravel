<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Item;
use App\Models\Party;
use Illuminate\Support\Facades\DB;

class AutoPurchaseController extends Controller
{
    public function storeAuto(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'party_name' => 'required|string',
            'place_of_supply' => 'nullable|string',
            'items' => 'required|array|min:1',

            'items.*.description' => 'required|string',
            'items.*.qty' => 'required|numeric',
            'items.*.price' => 'required|numeric',
            'items.*.gst_percent' => 'nullable|numeric',
            'items.*.hsn' => 'nullable|string',
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

            $grandTotal = $subtotal + $totalTax;

           
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
            $purchase = Purchase::create([
                'user_id' => $user->id,
                // 'purchase_number' => 'AUTO-' . time(),
                'purchase_number' => $purchaseNumber,
                'purchase_date' => now(),
                'party_id' => $party->id,
                'place_of_supply' => $data['place_of_supply'],
                'subtotal' => $subtotal,
                'total_tax' => $totalTax,
                'grand_total' => $grandTotal,
                'status' => 'paid',
            ]);

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
            return response()->json([
                'success' => true,
                'data' => [
                    'purchase' => $purchase->load(['items', 'party']),
                    'id' => $purchase->id,
                    'purchase_number' => $purchase->purchase_number,
                    'purchase_date' => $purchase->purchase_date,
                    'place_of_supply' => $purchase->place_of_supply,
                    'subtotal' => $purchase->subtotal,
                    'total_tax' => $purchase->total_tax,
                    'grand_total' => $purchase->grand_total,
                    'status' => $purchase->status,

                    'party' => [
                        'id' => $party->id,
                        'party_name' => $party->party_name,
                        'is_new' => $isNewParty, // ✅ PARTY FLAG
                        'contact_number' => $party->contact_number ?? '', // ✅
                    ],

                    'items' => $preparedItems, // ✅ ITEMS WITH FLAG
                ]
            ], 201);
        });
    }
}