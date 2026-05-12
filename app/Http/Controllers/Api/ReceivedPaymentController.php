<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReceivedPayment;
use App\Models\Party;
use App\Models\Invoice;

class ReceivedPaymentController extends Controller
{
    public function store(Request $request)
    {
        // ❌ payment_number REMOVED from validation
        $data = $request->validate([
            'party_id'     => 'required|exists:parties,id',
            'payment_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string',

            'invoices'              => 'nullable|array',
            'invoices.*.invoice_id' => 'required|exists:invoices,id',
            'invoices.*.amount'     => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($request, $data) {

            // 🔒 Lock party
            $party = Party::lockForUpdate()->findOrFail($data['party_id']);

            // =====================================================
            // 🔐 AUTO INCREMENT PAYMENT NUMBER (PER USER, SAFE)
            // =====================================================
            $lastPaymentNumber = ReceivedPayment::where('user_id', auth()->id())
                ->lockForUpdate()
                ->max('payment_number');

            $nextPaymentNumber = ($lastPaymentNumber ?? 0) + 1;

            // =====================================================
            // 🧾 SAVE RECEIVED PAYMENT
            // =====================================================
            $payment = ReceivedPayment::create([
                'user_id'        => auth()->id(),
                'party_id'       => $data['party_id'],
                'payment_date'   => $data['payment_date'],
                'payment_number' => $nextPaymentNumber, // ✅ AUTO
                'amount'         => $data['amount'],
                'payment_mode'   => $data['payment_mode'],
            ]);

            // =====================================================
            // 🔁 APPLY PAYMENT TO INVOICES
            // =====================================================
            foreach ($request->invoices ?? [] as $row) {

                $invoice = Invoice::lockForUpdate()
                    ->where('party_id', $party->id)
                    ->findOrFail($row['invoice_id']);

                $invoice->received_amount += $row['amount'];

                $invoice->balance_amount = max(
                    $invoice->grand_total - $invoice->received_amount,
                    0
                );

                if ($invoice->balance_amount <= 0) {
                    $invoice->status = 'paid';
                } elseif ($invoice->received_amount > 0) {
                    $invoice->status = 'partial';
                } else {
                    $invoice->status = 'unpaid';
                }

                $invoice->save();
            }

            // =====================================================
            // 🔁 UPDATE PARTY BALANCE
            // =====================================================
            if ($party->opening_balance_type === 'receive') {
                $party->opening_balance -= $data['amount'];
            } else {
                $party->opening_balance += $data['amount'];
            }

            if ($party->opening_balance < 0) {
                $party->opening_balance = 0;
            }

            $party->save();

            // =====================================================
            // ✅ RETURN PAYMENT NUMBER FOR UI DISPLAY
            // =====================================================
            return response()->json([
                'success'     => true,
                'data'        => $payment,
                // 'payment_no'  => $payment->payment_number, // 👈 IMPORTANT
                // 'payment_no'  => $payment->id,
                'payment_no'  => $payment->payment_number,
                'new_balance' => $party->opening_balance,
            ], 201);
        });
    }

//     public function nextNumber()
// {
//     return response()->json([
//         'next_number' => (ReceivedPayment::max('id') ?? 0) + 1
//     ]);
// }

public function nextNumber(Request $request)
{
    $user = $request->user();

    $nextNumber = ReceivedPayment::where('user_id', $user->id)
        ->max('payment_number');

    return response()->json([
        'next_number' => ($nextNumber ?? 0) + 1
    ]);
}

}
