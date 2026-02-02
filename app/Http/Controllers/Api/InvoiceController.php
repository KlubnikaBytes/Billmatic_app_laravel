<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceAdditionalCharge;
use App\Models\Item;
use App\Models\Party;
use Carbon\Carbon;


class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number',
            'invoice_date'   => 'required|date',
            'due_date'       => 'nullable|date',
            'party_id'       => 'required|exists:parties,id',
            'place_of_supply'=> 'nullable|string|max:100',
            'notes'          => 'nullable|string',

            // ✅ ADDITIONAL CHARGES
            'additional_charges' => 'nullable|array',
            'additional_charges.*.name'   => 'required|string|max:255',
            'additional_charges.*.amount' => 'required|numeric|min:0',

            // ✅ DISCOUNT
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount'  => 'nullable|numeric|min:0',

            'round_off'  => 'nullable|numeric',
            'tcs_amount' => 'nullable|numeric|min:0',

            // ✅ PAYMENT
            'received_amount' => 'nullable|numeric|min:0',
            'balance_amount'  => 'nullable|numeric|min:0',
            'payment_mode'    => 'nullable|string|max:50',

            // ✅ ITEMS
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.description' => 'nullable|string',
            'items.*.qty'         => 'required|numeric|min:0.01',
            'items.*.unit'        => 'nullable|string|max:50',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($user, $data) {

            $subtotal = 0;
            $totalTax = 0;

            // 🔐 1. ITEMS + STOCK DEDUCTION
            foreach ($data['items'] as &$line) {

                $item = Item::where('id', $line['item_id'])
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // if ($item->opening_stock < $line['qty']) {
                //     throw new \Exception("Insufficient stock for {$item->name}");
                // }

                // $item->opening_stock -= $line['qty'];
                // $item->save();

                // $qty   = $line['qty'];
                // $price = $line['price'];
                // $disc  = $line['discount'] ?? 0;
                // $gst   = $line['gst_percent'] ?? 0;

                // $lineAmount = ($qty * $price) - $disc;
                // $gstAmount  = $lineAmount * ($gst / 100);

                // $line['gst_amount'] = $gstAmount;
                // $line['line_total'] = $lineAmount + $gstAmount;

                // $subtotal += $lineAmount;
                // $totalTax += $gstAmount;

                $openingStock = $item->opening_stock;

if ($openingStock < $line['qty']) {
    throw new \Exception("Insufficient stock for {$item->name}");
}

// 🔻 Deduct stock
$item->opening_stock -= $line['qty'];
$item->save();

$closingStock = $item->opening_stock;

$qty   = $line['qty'];
$price = $line['price'];
$disc  = $line['discount'] ?? 0;
$gst   = $line['gst_percent'] ?? 0;

$lineAmount = ($qty * $price) - $disc;
$gstAmount  = $lineAmount * ($gst / 100);

// ✅ STORE SNAPSHOT
$line['opening_stock'] = $openingStock;
$line['closing_stock'] = $closingStock;
$line['gst_amount']    = $gstAmount;
$line['line_total']    = $lineAmount + $gstAmount;

$subtotal += $lineAmount;
$totalTax += $gstAmount;

            }

            // 🔢 2. ADDITIONAL CHARGES
            $additionalTotal = collect($data['additional_charges'] ?? [])
                ->sum('amount');

            // 🔢 3. DISCOUNT
            $discountPercent = $data['discount_percent'] ?? 0;
            $discountAmount  = $data['discount_amount'] ?? 0;

            if ($discountPercent > 0) {
                $discountAmount = ($subtotal * $discountPercent) / 100;
            }

            $roundOff = $data['round_off'] ?? 0;
            $tcs      = $data['tcs_amount'] ?? 0;

            // 🔢 4. GRAND TOTAL
            $grandTotal = $subtotal
                + $totalTax
                + $additionalTotal
                - $discountAmount
                + $roundOff
                + $tcs;

            // 🔢 5. PAYMENT
            // $receivedAmount = $data['received_amount'] ?? 0;
            // $balanceAmount  = $data['balance_amount']
            //     ?? ($grandTotal - $receivedAmount);

            $receivedAmount = $data['received_amount'] ?? 0;

            // 🚨 ALWAYS calculate balance from grand total
            $balanceAmount = max($grandTotal - $receivedAmount, 0);

            // ✅ FINAL STATUS LOGIC
            if ($receivedAmount <= 0) {
            $status = 'unpaid';
            } elseif ($balanceAmount <= 0) {
            $status = 'paid';
            } else {
            $status = 'partial';
            }


                
           // ✅ INVOICE STATUS (FINAL LOGIC)
           if ($balanceAmount <= 0) {
           $status = 'paid';
           } elseif ($receivedAmount <= 0) {
           $status = 'unpaid';
           } else {
           $status = 'partial';
           }

           // 🔐 FETCH PARTY OPENING BALANCE (SNAPSHOT)
$party = Party::lockForUpdate()->findOrFail($data['party_id']);
$partyOpeningBalance = $party->opening_balance;


            // 🧾 6. CREATE INVOICE
            $invoice = Invoice::create([
                'user_id'          => $user->id,
                'invoice_number'   => $data['invoice_number'],
                'invoice_date'     => $data['invoice_date'],
                'due_date'         => $data['due_date'] ?? null,
                'party_id'         => $data['party_id'],
                'party_opening_balance' => $partyOpeningBalance, // ✅ ADD HERE
                'place_of_supply'  => $data['place_of_supply'] ?? null,

                'subtotal'         => $subtotal,
                'total_tax'        => $totalTax,

                'discount_percent' => $discountPercent,
                'discount_amount'  => $discountAmount,
                'round_off'        => $roundOff,
                'tcs_amount'       => $tcs,

                // ✅ PAYMENT STORED
                'received_amount'  => $receivedAmount,
                'balance_amount'   => $balanceAmount,
                'payment_mode'     => $data['payment_mode'] ?? null,

                'status'           => $status, // ✅ IMPORTANT

                'grand_total'      => $grandTotal,
                'notes'            => $data['notes'] ?? null,
            ]);



            // =====================================================
// 🔁 UPDATE PARTY OPENING BALANCE BASED ON BALANCE AMOUNT
// =====================================================

// $party = Party::lockForUpdate()->findOrFail($data['party_id']);

$balanceAmount = $invoice->balance_amount ?? 0;

// if ($balanceAmount > 0) {
//     if ($party->opening_balance_type === 'receive') {
//         // 🟢 Customer owes you → increase receivable
//         $party->opening_balance += $balanceAmount;
//     } else {
//         // 🔴 You owe supplier → increase payable
//         $party->opening_balance -= $balanceAmount;
//     }

//     $party->save();

//     $partyClosingBalance = $party->opening_balance;

// $invoice->update([
//     'party_closing_balance' => $partyClosingBalance,
// ]);

// }

// 🔁 UPDATE PARTY BALANCE
if ($balanceAmount > 0) {
    if ($party->opening_balance_type === 'receive') {
        $party->opening_balance += $balanceAmount;
    } else {
        $party->opening_balance -= $balanceAmount;
    }
    $party->save();
}

// ✅ STORE PARTY CLOSING BALANCE SNAPSHOT (ALWAYS)
$invoice->update([
    'party_closing_balance' => $party->opening_balance,
]);



            // 🧾 7. SAVE ITEMS
            foreach ($data['items'] as $line) {
                $line['invoice_id'] = $invoice->id;
                InvoiceItem::create($line);
            }



            // 🧾 8. SAVE ADDITIONAL CHARGES
            if (!empty($data['additional_charges'])) {
                foreach ($data['additional_charges'] as $charge) {
                    InvoiceAdditionalCharge::create([
                        'invoice_id' => $invoice->id,
                        'name'       => $charge['name'],
                        'amount'     => $charge['amount'],
                    ]);
                }
            }

        
            return response()->json([
    'success' => true,
    'message' => 'Invoice created successfully',
    'data'    => [
        'invoice_number' => $invoice->invoice_number,
        'invoice_date'   => $invoice->invoice_date,
        'due_date'       => $invoice->due_date,

        'subtotal'    => $invoice->subtotal,
        'total_tax'   => $invoice->total_tax,
        'grand_total' => $invoice->grand_total,

        // ✅ ADD THIS
        'status' => $invoice->status,

        // ✅ Party
        'party' => $invoice->party,

        // ✅ ITEMS (FINAL & CORRECT)
        // 'items' => $invoice->items()
        //     ->with('item')
        //     ->get()
        //     ->map(function ($row) {
        //         return [
        //             'item_id'     => $row->item_id,
        //              'description' => $row->item->name, // ✅ ALWAYS CORRECT
        //             'qty'         => $row->qty,
        //             'price'       => $row->price,
        //             'line_total'  => $row->line_total,
        //         ];
        //     }),
        'items' => $invoice->items()
    ->with('item')
    ->get()
    ->map(function ($row) {
        return [
            'item_id'     => $row->item_id,
            'description' => $row->item->name,
            'hsn'         => $row->item->hsn_code ?? '',
            'qty'         => $row->qty,
            'unit'        => $row->unit,
            'price'       => $row->price,
            'gst_percent' => $row->gst_percent,
            'gst_amount'  => $row->gst_amount,
            'line_total'  => $row->line_total,
        ];
    }),


        'additional_charges' => $invoice->additionalCharges,
    ],
], 201);


        });
    }

    public function lastNumber()
    {
        $last = Invoice::select('invoice_number')
            ->where('invoice_number', 'LIKE', 'INV-%')
            ->orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")
            ->first();

        return response()->json([
            'last_number' => $last
                ? intval(str_replace('INV-', '', $last->invoice_number))
                : 0
        ]);
    }

public function salesSummary(Request $request)
{
    $user = $request->user();

    $range  = $request->get('range', 'this_week');
    $status = $request->get('status', 'all');
    $partyId = $request->get('party_id'); // ✅ NEW

    // 📅 Get date range
    [$start, $end] = $this->getDateRange($range, $request);

    // 🔎 Base query
    $query = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [$start, $end]);

    // 🟡 Status filter
    if ($status !== 'all') {
        $query->where('status', $status);
    }

    // 🟣 Party filter
    if (!empty($partyId)) {
        $query->where('party_id', $partyId);
    }

    // 📄 Fetch invoices
    $invoices = $query
        ->with('party')
        ->orderBy('invoice_date', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => [
            'range' => $range,
            'start' => $start->toDateString(),
            'end'   => $end->toDateString(),

            // 💰 Total (filtered)
            'total_sales' => round($invoices->sum('grand_total'), 2),

            // 📄 Invoice list
            'invoices' => $invoices->map(function ($inv) {
                return [
                    'invoice_id'     => $inv->id,
                    'invoice_number' => $inv->invoice_number,
                    'invoice_date'   => $inv->invoice_date,
                    'party_name'     => $inv->party->party_name ?? 'Cash Sale',
                    'amount'         => (float) $inv->grand_total,
                    'status'         => $inv->status,
                    'balance'        => (float) $inv->balance_amount,
                ];
            }),
        ],
    ]);
}



private function getDateRange($range, Request $request)
{
    $today = Carbon::today();

    return match ($range) {
        'today' => [$today, $today],

        'yesterday' => [
            $today->copy()->subDay(),
            $today->copy()->subDay(),
        ],

        'this_week' => [
            $today->copy()->startOfWeek(),
            $today->copy()->endOfWeek(),
        ],

        'last_week' => [
            $today->copy()->subWeek()->startOfWeek(),
            $today->copy()->subWeek()->endOfWeek(),
        ],

        'last_7_days' => [
            $today->copy()->subDays(6),
            $today,
        ],

        'this_month' => [
            $today->copy()->startOfMonth(),
            $today->copy()->endOfMonth(),
        ],

        'last_month' => [
            $today->copy()->subMonth()->startOfMonth(),
            $today->copy()->subMonth()->endOfMonth(),
        ],

        'this_quarter' => [
            $today->copy()->firstOfQuarter(),
            $today->copy()->lastOfQuarter(),
        ],

        'last_quarter' => [
            $today->copy()->subQuarter()->firstOfQuarter(),
            $today->copy()->subQuarter()->lastOfQuarter(),
        ],

        'current_fy' => [
            Carbon::create($today->year, 4, 1),
            Carbon::create($today->year + 1, 3, 31),
        ],

        'previous_fy' => [
            Carbon::create($today->year - 1, 4, 1),
            Carbon::create($today->year, 3, 31),
        ],

        'last_365_days' => [
            $today->copy()->subDays(364),
            $today,
        ],

        'custom' => [
            Carbon::parse($request->start),
            Carbon::parse($request->end),
        ],

        default => [
            $today->copy()->startOfWeek(),
            $today->copy()->endOfWeek(),
        ],
    };
}

public function cashBankSummary(Request $request)
{
    $user = $request->user();

    // ✅ Total received amount (Cash in hand)
    $cashInHand = Invoice::where('user_id', $user->id)
        ->sum('received_amount');

    return response()->json([
        'success' => true,
        'data' => [
            'total_balance' => (float) $cashInHand,
            'cash_in_hand'  => (float) $cashInHand,
        ],
    ]);
}

// public function cashBankDetails(Request $request)
// {
//     $user = $request->user();

//     $invoices = Invoice::where('user_id', $user->id)
//         ->where('received_amount', '>', 0)
//         ->orderBy('invoice_date', 'desc')
//         ->get();

//     return response()->json([
//         'success' => true,
//         'data' => $invoices->map(function ($inv) {
//             return [
//                 'date'            => $inv->invoice_date->format('d-m-Y'),
//                 'invoice_id'      => $inv->invoice_number,
//                 'party_name'      => $inv->party->party_name ?? 'Cash Sale',
//                 'received_amount' => (float) $inv->received_amount,
//             ];
//         }),
//     ]);
// }

public function cashBankDetails(Request $request)
{
    $user = $request->user();

    $range = $request->get('range', 'last_7_days');
    [$start, $end] = $this->getDateRange($range, $request);

    $invoices = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [$start, $end])
        ->where('received_amount', '>', 0)
        ->orderBy('invoice_date', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $invoices->map(function ($inv) {
            return [
                'date'            => $inv->invoice_date->format('d-m-Y'),
                'invoice_id'      => $inv->invoice_number,
                'party_name'      => $inv->party->party_name ?? 'Cash Sale',
                'received_amount' => (float) $inv->received_amount,
            ];
        }),
    ]);
}





   

    
}
