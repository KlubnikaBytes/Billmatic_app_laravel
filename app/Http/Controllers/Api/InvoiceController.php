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
//need to add
use App\Models\Purchase;
use App\Models\ReceivedPayment;

use Carbon\Carbon;
use App\Services\PaymentLinkService;
use App\Services\SmsService;
use App\Models\BusinessDetail;
//need to dd inn server
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ManagedUser;
use App\Models\User;
use Illuminate\Validation\Rule;




class InvoiceController extends Controller
{
    
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number',
            // 'invoice_number' => 'nullable|string|max:50',
            'invoice_date'   => 'required|date',
            'due_date'       => 'nullable|date',
            'party_id'       => 'required|exists:parties,id',
            'place_of_supply'=> 'nullable|string|max:100',
            'notes'          => 'nullable|string',

            // ✅ ADDITIONAL CHARGES
            'additional_charges' => 'nullable|array',
            // 'additional_charges.*.name'   => 'required|string|max:255',
            // 'additional_charges.*.amount' => 'required|numeric|min:0',
            'additional_charges.*.name'   => 'nullable|string|max:255',
            'additional_charges.*.amount' => 'nullable|numeric|min:0',

            // ✅ DISCOUNT
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount'  => 'nullable|numeric|min:0',

            'round_off'  => 'nullable|numeric',
            'tcs_amount' => 'nullable|numeric|min:0',

            // ✅ PAYMENT
            'received_amount' => 'nullable|numeric|min:0',
            // 'balance_amount'  => 'nullable|numeric|min:0',
            'payment_mode'    => 'nullable|string|max:50',

            //need to add//
            'send_sms' => 'nullable|boolean',

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

    //     $lastInvoice = Invoice::where('user_id', $user->id)
    //     ->latest('id')
    //     ->first();

    //     $nextNumber = 1;

    //    if (
    //    $lastInvoice &&
    //    preg_match('/INV-(\d+)/', $lastInvoice->invoice_number, $match)
    //    ) {
    //    $nextNumber = ((int) $match[1]) + 1;
    //    }

    //    $invoiceNumber = 'INV-' . $nextNumber;

            $subtotal = 0;
            $totalTax = 0;


        $preparedItems = [];

        foreach ($data['items'] as $line) {

        $item = Item::where('id', $line['item_id'])
        ->where('user_id', $user->id)
        ->lockForUpdate()
        ->firstOrFail();

    if ($item->opening_stock < $line['qty']) {
        throw new \Exception("Insufficient stock for {$item->name}");
    }

    $openingStock = $item->opening_stock;

    // 🔻 Deduct stock
    $item->opening_stock -= $line['qty'];
    $item->save();

    $qty   = $line['qty'];
    $price = $line['price'];
    $disc  = $line['discount'] ?? 0;
    $gst   = $line['gst_percent'] ?? 0;

    $lineAmount = ($qty * $price) - $disc;
    $gstAmount  = $lineAmount * ($gst / 100);

    $subtotal += $lineAmount;
    $totalTax += $gstAmount;

    // ✅ STORE IN NEW ARRAY (SAFE)
    $preparedItems[] = [
        'item_id'       => $line['item_id'],
        'description'   => $line['description'] ?? null,
        'qty'           => $qty,
        'unit'          => $line['unit'] ?? 'PCS',
        'price'         => $price,
        'discount'      => $disc,
        'gst_percent'   => $gst,
        'gst_amount'    => $gstAmount,
        'line_total'    => $lineAmount + $gstAmount,
        'opening_stock' => $openingStock,
        'closing_stock' => $item->opening_stock,
    ];
}


        //     // 🔢 2. ADDITIONAL CHARGES
        //     $additionalTotal = collect($data['additional_charges'] ?? [])
        //         ->sum('amount');

        //     // 🔢 3. DISCOUNT
        //     $discountPercent = $data['discount_percent'] ?? 0;
        //     $discountAmount  = $data['discount_amount'] ?? 0;

        //     if ($discountPercent > 0) {
        //         $discountAmount = ($subtotal * $discountPercent) / 100;
        //     }

        //     $roundOff = $data['round_off'] ?? 0;
        //     $tcs      = $data['tcs_amount'] ?? 0;

        //     // 🔢 4. GRAND TOTAL
        //     $grandTotal = $subtotal
        //         + $totalTax
        //         + $additionalTotal
        //         - $discountAmount
        //         + $roundOff
        //         + $tcs;


        //         $receivedAmount = $data['received_amount'] ?? 0;

        //     // ✅ MONEY NORMALIZATION (THIS IS CORRECT)
        //     $grandTotal     = round($grandTotal, 2);
        //     $receivedAmount = round($receivedAmount, 2);

        //     $balanceAmount = round($grandTotal - $receivedAmount, 2);

        //   // 🔐 Force zero for floating-point issues
        //    if (abs($balanceAmount) < 0.01) {
        //    $balanceAmount = 0;
        //    }

        //     // ✅ FINAL STATUS LOGIC (ONLY ONE ALLOWED)
        //     if ($balanceAmount <= 0) {
        //     $status = 'paid';
        //     } elseif ($receivedAmount <= 0) {
        //     $status = 'unpaid';
        //     } else {
        //     $status = 'partial';
        //     }

        //need to add
        // =====================================================
// 2. ADDITIONAL CHARGES
// =====================================================

$additionalTotal = collect($data['additional_charges'] ?? [])
    ->sum(function ($charge) {
        return (float) ($charge['amount'] ?? 0);
    });

$additionalTotal = round($additionalTotal, 2);


// =====================================================
// 3. DISCOUNT
// =====================================================

$discountPercent = (float) ($data['discount_percent'] ?? 0);

// Amount on which discount is calculated
// Subtotal + Tax + Additional Charges
$discountBase = $subtotal
    + $totalTax
    + $additionalTotal;

$discountAmount = 0;

if ($discountPercent > 0) {
    $discountAmount = ($discountBase * $discountPercent) / 100;
} else {
    // If percentage is not used, use manually supplied amount
    $discountAmount = (float) ($data['discount_amount'] ?? 0);
}

$discountAmount = round($discountAmount, 2);


// =====================================================
// 4. ROUND OFF & TCS
// =====================================================

$roundOff = round(
    (float) ($data['round_off'] ?? 0),
    2
);

$tcs = round(
    (float) ($data['tcs_amount'] ?? 0),
    2
);


// =====================================================
// 5. GRAND TOTAL
// =====================================================

$grandTotal =
      $subtotal
    + $totalTax
    + $additionalTotal
    - $discountAmount
    + $roundOff
    + $tcs;

$grandTotal = round($grandTotal, 2);


// =====================================================
// 6. RECEIVED AMOUNT
// =====================================================

$receivedAmount = round(
    (float) ($data['received_amount'] ?? 0),
    2
);


// =====================================================
// 7. BALANCE AMOUNT
// =====================================================

// Grand Total - Received Amount
$balanceAmount = round(
    $grandTotal - $receivedAmount,
    2
);

// Never show negative balance.
// If customer has paid equal to or more than Grand Total,
// balance is zero.
// if ($balanceAmount <= 0) {
//     $balanceAmount = 0.00;
// }


// =====================================================
// 8. STATUS
// =====================================================

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
                // 'invoice_number'   => $invoiceNumber,
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


        //     // ===============================
        //     // 🔗 CREATE PAYMENT LINK
        //     // ===============================
        //    $paymentLink = null;

        //    if ($invoice->balance_amount > 0 && $party->contact_number) {

        //  $paymentLink = PaymentLinkService::create($invoice, $party);

        //   $smsText =
        //    "Dear {$party->party_name}, your invoice {$invoice->invoice_number} "
        //  . "amount ₹{$invoice->balance_amount}. "
        //  . "Pay here: {$paymentLink}. Thank you.";

        //   SmsService::sendPaymentLink(
        //   $party->contact_number,
        //   $smsText
        //    );
        //     }

        // ===============================
// 🔗 CREATE PAYMENT LINK + SEND SMS
// // ===============================
// $paymentLink = null;

// if ($invoice->balance_amount > 0 && $party->contact_number) {

//     // Create Razorpay payment link
//     $paymentLink = PaymentLinkService::create($invoice, $party);
    

//     //need to add server
//     // ✅ ADD THIS LINE (VERY IMPORTANT)
//     $invoice->update([
//         'payment_link' => $paymentLink
//     ]);

//       // ✅ ADD THIS LOG RIGHT HERE 👇
//     \Log::info('SMS_TRIGGER', [
//         'mobile'  => $party->contact_number,
//         'amount'  => $invoice->balance_amount,
//         'invoice' => $invoice->invoice_number,
//         'link'    => $paymentLink,
//     ]);


//     // Send SMS using DLT template variables
//     SmsService::sendPaymentLink(
//         $party->contact_number,
//         [
//             $invoice->balance_amount,   // {#var#} → Amount
//             $invoice->invoice_number,   // {#var#} → Order / Invoice No
//             $paymentLink                // {#var#} → Payment link
//         ]
//     );
// }


$paymentLink = null;

if (
    ($data['send_sms'] ?? true) &&
    $invoice->balance_amount > 0 &&
    $party->contact_number
) {

    try {

        // ✅ Create Razorpay payment link
        $paymentLink = PaymentLinkService::create($invoice, $party);

        // ✅ Save payment link
        $invoice->update([
            'payment_link' => $paymentLink
        ]);

        // ✅ Log
        \Log::info('SMS_TRIGGER', [
            'mobile'  => $party->contact_number,
            'amount'  => $invoice->balance_amount,
            'invoice' => $invoice->invoice_number,
            'link'    => $paymentLink,
        ]);

        // ✅ Send SMS
        SmsService::sendPaymentLink(
            $party->contact_number,
            [
                $invoice->balance_amount,
                $invoice->invoice_number,
                $paymentLink
            ]
        );

    } catch (\Exception $e) {

        // ✅ Prevent crash
        \Log::error('RAZORPAY_ERROR', [
            'message' => $e->getMessage(),
        ]);

        $paymentLink = null;
    }
}





            // =====================================================
           // 🔁 UPDATE PARTY OPENING BALANCE BASED ON BALANCE AMOUNT
           // =====================================================

          $balanceAmount = $invoice->balance_amount ?? 0;

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



           
            // 🧾 7. SAVE ITEMS (SAFE)
    foreach ($preparedItems as $line) {
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

            // ✅ GET BUSINESS DETAILS
            $business = BusinessDetail::where('user_id', $user->id)->first();

        
          return response()->json([
    'success' => true,
    'message' => 'Invoice created successfully',
    'data'    => [
        //need to add
        'id' => $invoice->id,
        'invoice_number' => $invoice->invoice_number,
        'invoice_date'   => $invoice->invoice_date,
        'due_date'       => $invoice->due_date,

        'subtotal'    => (float) $invoice->subtotal,
        'total_tax'   => (float) $invoice->total_tax,
        'grand_total' => (float) $invoice->grand_total,
        'discount_amount' => (float) $invoice->discount_amount,
        'round_off'       => (float) $invoice->round_off,
        'tcs_amount'      => (float) $invoice->tcs_amount,

        // ✅ MUST SEND THESE
        'received_amount' => (float) $invoice->received_amount,
        'balance_amount'  => (float) $invoice->balance_amount,
        'payment_mode'    => $invoice->payment_mode,

        'status' => $invoice->status,
        'party'  => $invoice->party,

            // ✅ ADD THIS (BUSINESS INFO)
        'business' => [
            'industry' => $business->industry ?? '',
            'gstin'   => $business->gst_number ?? '',
            'address' => $business->address ?? '',
            'industry' => $business->industry ?? '',
    
            'city' => $business->city ?? '',
            'pan_cin' => $business->pan_cin ?? '',
            'trade_license_or_udyam' =>
        $business->trade_license_or_udyam ?? '',

        // need to add//
        // Business phone number
    'business_phone_number' =>
        ($business->show_phone_on_invoice ?? false)
            ? ($business->business_phone_number ?? '')
            : '',

         // Business email
    'business_email' => $business->business_email ?? '',

    // ✅ USER MOBILE
    'mobile' => $user->mobile ?? '',
        ],

         // ✅ ADD THIS LINE
        'payment_link' => $paymentLink,  

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

            //need to add
            'additional_charge_total' => (float)
    $invoice->additionalCharges->sum('amount'),

        'additional_charges' => $invoice->additionalCharges,
    ],
], 201);
});
    }

   public function managedStore(Request $request)
{
    $managedUser = $request->user('managed-api');

    abort_unless($managedUser, 401, 'Unauthenticated.');

    abort_unless(
        in_array($managedUser->role, ['Partner', 'Salesman'], true),
        403,
        'Your role cannot create invoices.'
    );

    $owner = User::findOrFail($managedUser->owner_user_id);
    $ownerId = $owner->id;

        $data = $request->validate([
            'invoice_number' => ['required','string','max:50',
            Rule::unique('invoices', 'invoice_number')
            ->where('user_id', $ownerId),],
            // 'invoice_number' => 'nullable|string|max:50',
            'invoice_date'   => 'required|date',
            'due_date'       => 'nullable|date',
           'party_id' => ['required',Rule::exists('parties', 'id')
           ->where('user_id', $ownerId),],
            'place_of_supply'=> 'nullable|string|max:100',
            'notes'          => 'nullable|string',

            // ✅ ADDITIONAL CHARGES
            'additional_charges' => 'nullable|array',
            // 'additional_charges.*.name'   => 'required|string|max:255',
            // 'additional_charges.*.amount' => 'required|numeric|min:0',
            'additional_charges.*.name'   => 'nullable|string|max:255',
            'additional_charges.*.amount' => 'nullable|numeric|min:0',

            // ✅ DISCOUNT
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount'  => 'nullable|numeric|min:0',

            'round_off'  => 'nullable|numeric',
            'tcs_amount' => 'nullable|numeric|min:0',

            // ✅ PAYMENT
            'received_amount' => 'nullable|numeric|min:0',
            // 'balance_amount'  => 'nullable|numeric|min:0',
            'payment_mode'    => 'nullable|string|max:50',

            //need to add//
            'send_sms' => 'nullable|boolean',

            // ✅ ITEMS
            'items'           => 'required|array|min:1',
            'items.*.item_id' => [ 'required',Rule::exists('items', 'id')
            ->where('user_id', $ownerId),],
            'items.*.description' => 'nullable|string',
            'items.*.qty'         => 'required|numeric|min:0.01',
            'items.*.unit'        => 'nullable|string|max:50',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
        ]);

    


        return DB::transaction(function () use ($owner, $ownerId, $data) {

    //     $lastInvoice = Invoice::where('user_id', $user->id)
    //     ->latest('id')
    //     ->first();

    //     $nextNumber = 1;

    //    if (
    //    $lastInvoice &&
    //    preg_match('/INV-(\d+)/', $lastInvoice->invoice_number, $match)
    //    ) {
    //    $nextNumber = ((int) $match[1]) + 1;
    //    }

    //    $invoiceNumber = 'INV-' . $nextNumber;

            $subtotal = 0;
            $totalTax = 0;


        $preparedItems = [];

        foreach ($data['items'] as $line) {
        $item = Item::where('id', $line['item_id'])
        ->where('user_id', $ownerId)
        ->lockForUpdate()
        ->firstOrFail();

    if ($item->opening_stock < $line['qty']) {
        throw new \Exception("Insufficient stock for {$item->name}");
    }

    $openingStock = $item->opening_stock;

    // 🔻 Deduct stock
    $item->opening_stock -= $line['qty'];
    $item->save();

    $qty   = $line['qty'];
    $price = $line['price'];
    $disc  = $line['discount'] ?? 0;
    $gst   = $line['gst_percent'] ?? 0;

    $lineAmount = ($qty * $price) - $disc;
    $gstAmount  = $lineAmount * ($gst / 100);

    $subtotal += $lineAmount;
    $totalTax += $gstAmount;

    // ✅ STORE IN NEW ARRAY (SAFE)
    $preparedItems[] = [
        'item_id'       => $line['item_id'],
        'description'   => $line['description'] ?? null,
        'qty'           => $qty,
        'unit'          => $line['unit'] ?? 'PCS',
        'price'         => $price,
        'discount'      => $disc,
        'gst_percent'   => $gst,
        'gst_amount'    => $gstAmount,
        'line_total'    => $lineAmount + $gstAmount,
        'opening_stock' => $openingStock,
        'closing_stock' => $item->opening_stock,
    ];
}


        //     // 🔢 2. ADDITIONAL CHARGES
        //     $additionalTotal = collect($data['additional_charges'] ?? [])
        //         ->sum('amount');

        //     // 🔢 3. DISCOUNT
        //     $discountPercent = $data['discount_percent'] ?? 0;
        //     $discountAmount  = $data['discount_amount'] ?? 0;

        //     if ($discountPercent > 0) {
        //         $discountAmount = ($subtotal * $discountPercent) / 100;
        //     }

        //     $roundOff = $data['round_off'] ?? 0;
        //     $tcs      = $data['tcs_amount'] ?? 0;

        //     // 🔢 4. GRAND TOTAL
        //     $grandTotal = $subtotal
        //         + $totalTax
        //         + $additionalTotal
        //         - $discountAmount
        //         + $roundOff
        //         + $tcs;


        //         $receivedAmount = $data['received_amount'] ?? 0;

        //     // ✅ MONEY NORMALIZATION (THIS IS CORRECT)
        //     $grandTotal     = round($grandTotal, 2);
        //     $receivedAmount = round($receivedAmount, 2);

        //     $balanceAmount = round($grandTotal - $receivedAmount, 2);

        //   // 🔐 Force zero for floating-point issues
        //    if (abs($balanceAmount) < 0.01) {
        //    $balanceAmount = 0;
        //    }

        //     // ✅ FINAL STATUS LOGIC (ONLY ONE ALLOWED)
        //     if ($balanceAmount <= 0) {
        //     $status = 'paid';
        //     } elseif ($receivedAmount <= 0) {
        //     $status = 'unpaid';
        //     } else {
        //     $status = 'partial';
        //     }

        //need to add
        // =====================================================
// 2. ADDITIONAL CHARGES
// =====================================================

$additionalTotal = collect($data['additional_charges'] ?? [])
    ->sum(function ($charge) {
        return (float) ($charge['amount'] ?? 0);
    });

$additionalTotal = round($additionalTotal, 2);


// =====================================================
// 3. DISCOUNT
// =====================================================

$discountPercent = (float) ($data['discount_percent'] ?? 0);

// Amount on which discount is calculated
// Subtotal + Tax + Additional Charges
$discountBase = $subtotal
    + $totalTax
    + $additionalTotal;

$discountAmount = 0;

if ($discountPercent > 0) {
    $discountAmount = ($discountBase * $discountPercent) / 100;
} else {
    // If percentage is not used, use manually supplied amount
    $discountAmount = (float) ($data['discount_amount'] ?? 0);
}

$discountAmount = round($discountAmount, 2);


// =====================================================
// 4. ROUND OFF & TCS
// =====================================================

$roundOff = round(
    (float) ($data['round_off'] ?? 0),
    2
);

$tcs = round(
    (float) ($data['tcs_amount'] ?? 0),
    2
);


// =====================================================
// 5. GRAND TOTAL
// =====================================================

$grandTotal =
      $subtotal
    + $totalTax
    + $additionalTotal
    - $discountAmount
    + $roundOff
    + $tcs;

$grandTotal = round($grandTotal, 2);


// =====================================================
// 6. RECEIVED AMOUNT
// =====================================================

$receivedAmount = round(
    (float) ($data['received_amount'] ?? 0),
    2
);


// =====================================================
// 7. BALANCE AMOUNT
// =====================================================

// Grand Total - Received Amount
$balanceAmount = round(
    $grandTotal - $receivedAmount,
    2
);

// Never show negative balance.
// If customer has paid equal to or more than Grand Total,
// balance is zero.
// if ($balanceAmount <= 0) {
//     $balanceAmount = 0.00;
// }


// =====================================================
// 8. STATUS
// =====================================================

if ($balanceAmount <= 0) {

    $status = 'paid';

} elseif ($receivedAmount <= 0) {

    $status = 'unpaid';

} else {

    $status = 'partial';
}


           // 🔐 FETCH PARTY OPENING BALANCE (SNAPSHOT)
           $party = Party::where('user_id', $ownerId)->lockForUpdate()->findOrFail($data['party_id']);
           $partyOpeningBalance = $party->opening_balance;


            // 🧾 6. CREATE INVOICE
            $invoice = Invoice::create([
                'user_id' => $ownerId,
                'invoice_number'   => $data['invoice_number'],
                // 'invoice_number'   => $invoiceNumber,
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


        //     // ===============================
        //     // 🔗 CREATE PAYMENT LINK
        //     // ===============================
        //    $paymentLink = null;

        //    if ($invoice->balance_amount > 0 && $party->contact_number) {

        //  $paymentLink = PaymentLinkService::create($invoice, $party);

        //   $smsText =
        //    "Dear {$party->party_name}, your invoice {$invoice->invoice_number} "
        //  . "amount ₹{$invoice->balance_amount}. "
        //  . "Pay here: {$paymentLink}. Thank you.";

        //   SmsService::sendPaymentLink(
        //   $party->contact_number,
        //   $smsText
        //    );
        //     }

        // ===============================
// 🔗 CREATE PAYMENT LINK + SEND SMS
// // ===============================
// $paymentLink = null;

// if ($invoice->balance_amount > 0 && $party->contact_number) {

//     // Create Razorpay payment link
//     $paymentLink = PaymentLinkService::create($invoice, $party);
    

//     //need to add server
//     // ✅ ADD THIS LINE (VERY IMPORTANT)
//     $invoice->update([
//         'payment_link' => $paymentLink
//     ]);

//       // ✅ ADD THIS LOG RIGHT HERE 👇
//     \Log::info('SMS_TRIGGER', [
//         'mobile'  => $party->contact_number,
//         'amount'  => $invoice->balance_amount,
//         'invoice' => $invoice->invoice_number,
//         'link'    => $paymentLink,
//     ]);


//     // Send SMS using DLT template variables
//     SmsService::sendPaymentLink(
//         $party->contact_number,
//         [
//             $invoice->balance_amount,   // {#var#} → Amount
//             $invoice->invoice_number,   // {#var#} → Order / Invoice No
//             $paymentLink                // {#var#} → Payment link
//         ]
//     );
// }


$paymentLink = null;

if (
    ($data['send_sms'] ?? true) &&
    $invoice->balance_amount > 0 &&
    $party->contact_number
) {

    try {

        // ✅ Create Razorpay payment link
        $paymentLink = PaymentLinkService::create($invoice, $party);

        // ✅ Save payment link
        $invoice->update([
            'payment_link' => $paymentLink
        ]);

        // ✅ Log
        \Log::info('SMS_TRIGGER', [
            'mobile'  => $party->contact_number,
            'amount'  => $invoice->balance_amount,
            'invoice' => $invoice->invoice_number,
            'link'    => $paymentLink,
        ]);

        // ✅ Send SMS
        SmsService::sendPaymentLink(
            $party->contact_number,
            [
                $invoice->balance_amount,
                $invoice->invoice_number,
                $paymentLink
            ]
        );

    } catch (\Exception $e) {

        // ✅ Prevent crash
        \Log::error('RAZORPAY_ERROR', [
            'message' => $e->getMessage(),
        ]);

        $paymentLink = null;
    }
}





            // =====================================================
           // 🔁 UPDATE PARTY OPENING BALANCE BASED ON BALANCE AMOUNT
           // =====================================================

          $balanceAmount = $invoice->balance_amount ?? 0;

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



           
            // 🧾 7. SAVE ITEMS (SAFE)
    foreach ($preparedItems as $line) {
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

            // ✅ GET BUSINESS DETAILS
            $business = BusinessDetail::where('user_id', $ownerId)->first();

        
          return response()->json([
    'success' => true,
    'message' => 'Invoice created successfully',
    'data'    => [
        //need to add
        'id' => $invoice->id,
        'invoice_number' => $invoice->invoice_number,
        'invoice_date'   => $invoice->invoice_date,
        'due_date'       => $invoice->due_date,

        'subtotal'    => (float) $invoice->subtotal,
        'total_tax'   => (float) $invoice->total_tax,
        'grand_total' => (float) $invoice->grand_total,
        'discount_amount' => (float) $invoice->discount_amount,
        'round_off'       => (float) $invoice->round_off,
        'tcs_amount'      => (float) $invoice->tcs_amount,

        // ✅ MUST SEND THESE
        'received_amount' => (float) $invoice->received_amount,
        'balance_amount'  => (float) $invoice->balance_amount,
        'payment_mode'    => $invoice->payment_mode,

        'status' => $invoice->status,
        'party'  => $invoice->party,

            // ✅ ADD THIS (BUSINESS INFO)
        'business' => [
            'industry' => $business->industry ?? '',
            'gstin'   => $business->gst_number ?? '',
            'address' => $business->address ?? '',
            'industry' => $business->industry ?? '',
    
            'city' => $business->city ?? '',
            'pan_cin' => $business->pan_cin ?? '',
            'trade_license_or_udyam' =>
        $business->trade_license_or_udyam ?? '',

        // need to add//
        // Business phone number
    'business_phone_number' =>
        ($business->show_phone_on_invoice ?? false)
            ? ($business->business_phone_number ?? '')
            : '',

         // Business email
    'business_email' => $business->business_email ?? '',

    // ✅ USER MOBILE
    'mobile' => $owner->mobile ?? '',
        ],

         // ✅ ADD THIS LINE
        'payment_link' => $paymentLink,  

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

            //need to add
            'additional_charge_total' => (float)
    $invoice->additionalCharges->sum('amount'),

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

//     public function lastNumber(Request $request)
// {
//     $user = $request->user();

//     $last = Invoice::where('user_id', $user->id)
//         ->where('invoice_number', 'LIKE', 'INV-%')
//         ->orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")
//         ->first();

//     return response()->json([
//         'last_number' => $last
//             ? intval(str_replace('INV-', '', $last->invoice_number))
//             : 0
//     ]);
// }

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
                    'due_date'       => $inv->due_date, // ✅ IMPORTANT
                    'party_name'     => $inv->party->party_name ?? 'Cash Sale',
                    'amount'         => (float) $inv->grand_total,
                    'status'         => $inv->status,
                    'balance'        => (float) $inv->balance_amount,
                ];
            }),
        ],
    ]);
}

//need to add//

public function billWiseProfit(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'start' => 'required|date',
        'end' => 'required|date|after_or_equal:start',
    ]);

    $invoices = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [
            $validated['start'],
            $validated['end'],
        ])
        ->with(['party', 'items.item'])
        ->orderByDesc('invoice_date')
        ->orderByDesc('id')
        ->get();

    $rows = $invoices->map(function ($invoice) {
        $salesBeforeTax = 0;
        $costTotal = 0;

        foreach ($invoice->items as $line) {
            $qty = (float) ($line->qty ?? 0);
            $price = (float) ($line->price ?? 0);
            $discount = (float) ($line->discount ?? 0);

            // Discount is treated as the total discount for this line.
            $lineSales = ($qty * $price) - $discount;

            // Uses the item's current purchase price.
            $purchasePrice = (float) (
                $line->item->purchase_price ?? 0
            );

            $salesBeforeTax += $lineSales;
            $costTotal += $qty * $purchasePrice;
        }

        $profit = round($salesBeforeTax - $costTotal, 2);

        return [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'invoice_date' => $invoice->invoice_date,
            'party_name' => $invoice->party->party_name
                ?? 'Cash Sale',
            'amount' => (float) $invoice->grand_total,
            'sales_before_tax' => round($salesBeforeTax, 2),
            'cost_total' => round($costTotal, 2),
            'profit' => $profit,
        ];
    });

    return response()->json([
        'success' => true,
        'data' => [
            'start' => $validated['start'],
            'end' => $validated['end'],
            'total_sales' => round(
                $rows->sum('amount'),
                2
            ),
            'total_profit' => round(
                $rows->sum('profit'),
                2
            ),
            'invoices' => $rows->values(),
        ],
    ]);
}


//need to add//
public function profitLoss(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'start' => 'required|date',
        'end' => 'required|date|after_or_equal:start',
    ]);

    $invoices = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [
            $validated['start'],
            $validated['end'],
        ])
        ->with('items.item')
        ->get();

    $sales = 0;
    $costOfGoodsSold = 0;

    foreach ($invoices as $invoice) {
        foreach ($invoice->items as $line) {
            $qty = (float) ($line->qty ?? 0);
            $price = (float) ($line->price ?? 0);
            $discount = (float) ($line->discount ?? 0);

            $sales += ($qty * $price) - $discount;

            $purchasePrice = (float) (
                $line->item->purchase_price ?? 0
            );

            $costOfGoodsSold += $qty * $purchasePrice;
        }
    }

    $sales = round($sales, 2);
    $costOfGoodsSold = round($costOfGoodsSold, 2);
    $grossProfit = round($sales - $costOfGoodsSold, 2);

    // Connect these to actual transaction records when an
    // other-income/expense module is available.
    $otherIncome = 0;
    $otherExpenses = 0;

    $totalIncome = round($sales + $otherIncome, 2);
    $totalExpenses = round(
        $costOfGoodsSold + $otherExpenses,
        2
    );

    $netProfit = round(
        $totalIncome - $totalExpenses,
        2
    );

    return response()->json([
        'success' => true,
        'data' => [
            'start' => $validated['start'],
            'end' => $validated['end'],
            'sales' => $sales,
            'other_income' => $otherIncome,
            'total_income' => $totalIncome,
            'cost_of_goods_sold' => $costOfGoodsSold,
            'gross_profit' => $grossProfit,
            'other_expenses' => $otherExpenses,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
        ],
    ]);
}

//need to add//
public function balanceSheet(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'as_of' => 'required|date',
    ]);

    $asOfDate = $validated['as_of'];

    $stockValue = Item::where('user_id', $user->id)
        ->get()
        ->sum(function ($item) {
            return (float) ($item->opening_stock ?? 0)
                * (float) ($item->purchase_price ?? 0);
        });

    $receivables = Invoice::where('user_id', $user->id)
        ->whereDate('invoice_date', '<=', $asOfDate)
        ->sum('balance_amount');

    $payables = Purchase::where('user_id', $user->id)
        ->whereDate('purchase_date', '<=', $asOfDate)
        ->sum('balance_amount');

    $stockValue = round((float) $stockValue, 2);
    $receivables = round((float) $receivables, 2);
    $payables = round((float) $payables, 2);

    $totalAssets = round(
        $stockValue + $receivables,
        2
    );

    $totalLiabilities = $payables;

    $netPosition = round(
        $totalAssets - $totalLiabilities,
        2
    );

    return response()->json([
        'success' => true,
        'data' => [
            'as_of' => $asOfDate,
            'stock_value' => $stockValue,
            'receivables' => $receivables,
            'total_assets' => $totalAssets,
            'payables' => $payables,
            'total_liabilities' => $totalLiabilities,
            'net_position' => $netPosition,
        ],
    ]);
}

public function daybook(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'start' => 'required|date',
        'end' => 'required|date|after_or_equal:start',
    ]);

    $start = $validated['start'];
    $end = $validated['end'];

    // 1. Sales invoices
    $sales = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [$start, $end])
        ->with('party:id,party_name')
        ->get()
        ->map(function ($invoice) {
            return [
                'type' => 'sale',
                'voucher_type' => 'Sales',
                'number' => $invoice->invoice_number,
                'date' => $invoice->invoice_date,
                'party_name' =>
                    $invoice->party->party_name ?? 'Cash Sale',
                'debit' => 0,
                'credit' => (float) $invoice->grand_total,
                'payment_mode' => $invoice->payment_mode ?? '',
            ];
        });

    // 2. Purchases
    $purchases = Purchase::where('user_id', $user->id)
        ->whereBetween('purchase_date', [$start, $end])
        ->with('party:id,party_name')
        ->get()
        ->map(function ($purchase) {
            return [
                'type' => 'purchase',
                'voucher_type' => 'Purchase',
                'number' => $purchase->purchase_number,
                'date' => $purchase->purchase_date,
                'party_name' =>
                    $purchase->party->party_name ?? 'Supplier',
                'debit' => (float) $purchase->grand_total,
                'credit' => 0,
                'payment_mode' => $purchase->payment_mode ?? '',
            ];
        });

    // 3. Received payments
    $payments = ReceivedPayment::where('user_id', $user->id)
        ->whereBetween('payment_date', [$start, $end])
        ->with('party:id,party_name')
        ->get()
        ->map(function ($payment) {
            return [
                'type' => 'payment',
                'voucher_type' => 'Receipt',
                'number' => $payment->payment_number,
                'date' => $payment->payment_date,
                'party_name' =>
                    $payment->party->party_name ?? 'Cash Sale',
                'debit' => (float) $payment->amount,
                'credit' => 0,
                'payment_mode' => $payment->payment_mode ?? '',
            ];
        });

    // Combine all vouchers and sort newest first.
    $entries = $sales
        ->concat($purchases)
        ->concat($payments)
        ->sortByDesc('date')
        ->values();

    return response()->json([
        'success' => true,
        'data' => [
            'start' => $start,
            'end' => $end,
            'total_debit' => round(
                $entries->sum('debit'),
                2
            ),
            'total_credit' => round(
                $entries->sum('credit'),
                2
            ),
            'entries' => $entries,
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

//need to add in server
// public function gstReport(Request $request)
// {
//     $user = auth()->user(); // ✅ CHANGE THIS

//     if (!$user) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Unauthorized',
//         ], 401);
//     }

//     $invoices = Invoice::where('user_id', $user->id)
//         ->with('party')
//         ->orderBy('invoice_date', 'desc')
//         ->get();

//     return response()->json([
//         'success' => true,
//         'count' => $invoices->count(), // ✅ DEBUG
//         'data' => $invoices->map(function ($inv) {
//          return [
//     'invoice_no' => $inv->invoice_number,
//     'date' => $inv->invoice_date,
//     'party_name' => $inv->party->party_name ?? '',
//     'place_of_supply' => $inv->place_of_supply ?? '',

//     'invoice_value' => (float) $inv->grand_total,
//     'taxable_value' => (float) $inv->subtotal,

//     'gst_percent' => $inv->subtotal > 0
//         ? round(($inv->total_tax / $inv->subtotal) * 100, 2)
//         : 0,

//     'cgst' => round($inv->total_tax / 2, 2),
//     'sgst' => round($inv->total_tax / 2, 2),
//     'igst' => 0, // (you can add logic later)

//     'total_tax' => (float) $inv->total_tax,
// ];
//         }),
//     ]);
// }

public function gstReport(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    // ✅ GET RANGE (default last_month)
    $range = $request->get('range', 'last_month');

    // ✅ USE YOUR EXISTING FUNCTION
    [$start, $end] = $this->getDateRange($range, $request);

    // ✅ GET BUSINESS DETAILS
    $business = BusinessDetail::where('user_id', $user->id)->first();


    // ✅ FILTER BY DATE
    $invoices = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [$start, $end])
        ->with('party')
        ->orderBy('invoice_date', 'desc')
        ->get();

    return response()->json([
        'success' => true,

        // ✅ ADD THIS
        'business' => [
            'name'   => $business->business_name ?? '',
            'mobile' => $business->mobile ?? '',
            'gst'    => $business->gst_number ?? '',
        ],

        // ✅ IMPORTANT (for Flutter UI)
        'start' => $start->toDateString(),
        'end'   => $end->toDateString(),

        'count' => $invoices->count(),

        'data' => $invoices->map(function ($inv) {
            return [
                'invoice_no' => $inv->invoice_number,
                'date' => $inv->invoice_date,
                'party_name' => $inv->party->party_name ?? '',
                'place_of_supply' => $inv->place_of_supply ?? '',

                'invoice_value' => (float) $inv->grand_total,
                'taxable_value' => (float) $inv->subtotal,

                'gst_percent' => $inv->subtotal > 0
                    ? round(($inv->total_tax / $inv->subtotal) * 100, 2)
                    : 0,

                'cgst' => round($inv->total_tax / 2, 2),
                'sgst' => round($inv->total_tax / 2, 2),
                'igst' => 0,

                'total_tax' => (float) $inv->total_tax,
            ];
        }),
    ]);
}


public function gstReportPdf(Request $request)
{
    $user = auth()->user();

    $range = $request->get('range', 'last_month');
    [$start, $end] = $this->getDateRange($range, $request);

    $business = BusinessDetail::where('user_id', $user->id)->first();

    $invoices = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [$start, $end])
        ->with('party')
        ->orderBy('invoice_date', 'desc')
        ->get();

    $pdf = Pdf::loadView('pdf.gst_report', [
        'invoices' => $invoices,
        'start' => $start->format('d/m/Y'),
        'end' => $end->format('d/m/Y'),
        'business' => $business,
    ]);

    return $pdf->stream('gst_report.pdf'); // ✅ IMPORTANT
}

//need to add in server
public function salesSummaryPdf(Request $request)
{
    $user = $request->user();

    $range  = $request->get('range', 'this_week');
    $status = $request->get('status', 'all');
    $partyId = $request->get('party_id');

    [$start, $end] = $this->getDateRange($range, $request);

    $query = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [$start, $end]);

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    if ($partyId) {
        $query->where('party_id', $partyId);
    }

    $invoices = $query->with('party')->get();

    $total = $invoices->sum('grand_total');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sales_summary', [
        'invoices' => $invoices,
        'start' => $start->format('d/m/Y'),
        'end' => $end->format('d/m/Y'),
        'total' => $total,
    ]);

    return $pdf->download('sales_report.pdf');
}

public function show($id)
{
    $invoice = Invoice::with([
        'party',
        'items.item',
        'additionalCharges'
    ])->findOrFail($id);

    $business = BusinessDetail::where(
        'user_id',
        $invoice->user_id
    )->first();

    return response()->json([
        'success' => true,
        'data' => [

            'invoice_number' => $invoice->invoice_number,
            'invoice_date'   => $invoice->invoice_date,
            'due_date'       => $invoice->due_date,

            'subtotal'    => (float) $invoice->subtotal,
            'total_tax'   => (float) $invoice->total_tax,
            'grand_total' => (float) $invoice->grand_total,

            'discount_amount' =>
                (float) $invoice->discount_amount,

            'round_off' =>
                (float) $invoice->round_off,

            'tcs_amount' =>
                (float) $invoice->tcs_amount,

            'received_amount' =>
                (float) $invoice->received_amount,

            'balance_amount' =>
                (float) $invoice->balance_amount,

            'status' => $invoice->status,

            'payment_link' => $invoice->payment_link,

            'party' => $invoice->party,

            'business' => [
                'industry' =>
                    $business->industry ?? '',

                'gstin' =>
                    $business->gst_number ?? '',

                'address' =>
                    $business->address ?? '',

                'city' =>
                    $business->city ?? '',

                'pan_cin' =>
                    $business->pan_cin ?? '',

                'trade_license_or_udyam' =>
                    $business->trade_license_or_udyam ?? '',

                //need to add//    

                'business_phone_number' =>
    ($business->show_phone_on_invoice ?? false)
        ? ($business->business_phone_number ?? '')
        : '',

'business_email' => $business->business_email ?? '',    

                'mobile' =>
                    $business->mobile ?? '',
                  //need to add//
                 // ✅ VERY IMPORTANT
                'invoice_format' =>
                    $business->invoice_format ?? 'A4 Size - Template 1',  
                    
                'invoice_theme' => $business->invoice_theme ?? 'None',
                'invoice_template' => $business->invoice_template ?? 'luxury',
                'invoice_theme_color' => $business->invoice_theme_color ?? '#C62828',    
            ],

            'items' => $invoice->items->map(function ($row) {
                return [
                    'item_id'     => $row->item_id,
                    'description' => $row->item->name ?? '',
                    'hsn'         => $row->item->hsn_code ?? '',
                    'qty'         => $row->qty,
                    'unit'        => $row->unit,
                    'price'       => $row->price,
                    'gst_percent' => $row->gst_percent,
                    'gst_amount'  => $row->gst_amount,
                    'line_total'  => $row->line_total,
                ];
            }),

            'additional_charges' =>
                $invoice->additionalCharges,
        ]
    ]);
}


//need to add
// // =====================================================
// INVOICE PREVIEW
// =====================================================
public function preview(Request $request, $id)
{
    $invoice = Invoice::with([
        'party',
        'items.item',
        'additionalCharges'
    ])->findOrFail($id);

    $business = BusinessDetail::where(
        'user_id',
        $invoice->user_id
    )->first();

    return view('invoice.preview', [
        'invoice'  => $invoice,
        'business' => $business,
    ]);
}

}
