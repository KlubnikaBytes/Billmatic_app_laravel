<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentOut;
use App\Models\PaymentOutAllocation;
use App\Models\Party;
use App\Models\Purchase;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PaymentOutController extends Controller
{
    // GET: Generate next Payment Out number
    public function nextNumber(Request $request)
    {
        $user = $request->user();

        $lastNumber = PaymentOut::where('user_id', $user->id)
            ->max('payment_number');

        return response()->json([
            'success' => true,
            'next_number' => ($lastNumber ?? 0) + 1,
        ]);
    }

    // GET: Unpaid and partially paid purchase invoices
    public function unpaidPurchases(Request $request)
    {
        $request->validate([
            'party_id' => 'required|integer',
        ]);

        $user = $request->user();

        $party = Party::where('user_id', $user->id)
            ->findOrFail($request->party_id);

        $purchases = Purchase::where('user_id', $user->id)
            ->where('party_id', $party->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('balance_amount', '>', 0)
            ->orderBy('purchase_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $purchases->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'purchase_number' => $purchase->purchase_number,
                    'purchase_date' => $purchase->purchase_date,
                    'grand_total' => (float) $purchase->grand_total,
                    'received_amount' => (float) $purchase->received_amount,
                    'balance_amount' => (float) $purchase->balance_amount,
                    'status' => $purchase->status,
                ];
            }),
        ]);
    }

    // POST: Save Payment Out & Update Party Balance
    public function store(Request $request)
    {
        $data = $request->validate([
            'party_id' => 'required|integer|exists:parties,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'required|string|max:50',
            'notes' => 'nullable|string',

            'purchases' => 'nullable|array',
            'purchases.*.purchase_id' => 'required|integer',
            'purchases.*.amount' => 'required|numeric|min:0',
            'purchases.*.discount_amount' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($data, $user) {

            // Lock user row to serialize number generation
            DB::table('users')
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();

            // Verify party belongs to this user and lock row for balance update
            $party = Party::where('user_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($data['party_id']);

            $discount = round((float) ($data['discount_amount'] ?? 0), 2);
            $amount = round((float) $data['amount'], 2);

            $allocations = $data['purchases'] ?? [];

            // Validate allocation totals (allow overpayment/advance payment)
            $allocatedAmount = 0;
            $allocatedDiscount = 0;

            foreach ($allocations as $row) {
                $allocatedAmount += (float) $row['amount'];
                $allocatedDiscount += (float) ($row['discount_amount'] ?? 0);
            }

            if ($allocations) {
                if (round($allocatedAmount, 2) > $amount + 0.001 ||
                    round($allocatedDiscount, 2) > $discount + 0.001) {
                    abort(422, 'Purchase allocations cannot exceed the payment amount and discount.');
                }
            }

            // Generate next Payment Out number
            $lastNumber = PaymentOut::where('user_id', $user->id)
                ->max('payment_number');

            $nextNumber = ($lastNumber ?? 0) + 1;

            // Create Payment Out record
            $payment = PaymentOut::create([
                'user_id' => $user->id,
                'party_id' => $party->id,
                'payment_number' => $nextNumber,
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'discount_amount' => $discount,
                'payment_mode' => $data['payment_mode'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Apply payment to selected purchase invoices
            foreach ($allocations as $row) {

                $purchase = Purchase::where('user_id', $user->id)
                    ->where('party_id', $party->id)
                    ->lockForUpdate()
                    ->findOrFail($row['purchase_id']);

                $paid = round((float) $row['amount'], 2);
                $invoiceDiscount = round((float) ($row['discount_amount'] ?? 0), 2);
                $settled = $paid + $invoiceDiscount;

                if ($paid <= 0 && $invoiceDiscount <= 0) {
                    abort(422, 'Each purchase allocation must have a positive amount.');
                }

                if ($settled > (float) $purchase->balance_amount + 0.001) {
                    abort(422, 'Payment exceeds the purchase invoice outstanding amount.');
                }

                // Update purchase settlement
                $purchase->received_amount = round(
                    (float) $purchase->received_amount + $settled,
                    2
                );

                $purchase->balance_amount = max(
                    round(
                        (float) $purchase->grand_total - (float) $purchase->received_amount,
                        2
                    ),
                    0
                );

                if ($purchase->balance_amount <= 0.01) {
                    $purchase->balance_amount = 0;
                    $purchase->status = 'paid';
                } elseif ($purchase->received_amount > 0) {
                    $purchase->status = 'partial';
                } else {
                    $purchase->status = 'unpaid';
                }

                $purchase->save();

                PaymentOutAllocation::create([
                    'payment_out_id' => $payment->id,
                    'purchase_id' => $purchase->id,
                    'amount' => $paid,
                    'discount_amount' => $invoiceDiscount,
                ]);
            }

            // =========================================================
            // UPDATE PARTY OPENING BALANCE & OPENING BALANCE TYPE
            // Payable ('pay') = negative, Receivable ('receive') = positive
            // =========================================================
            $currentBalance = abs((float) ($party->opening_balance ?? 0));
            $currentType = strtolower(trim($party->opening_balance_type ?? 'pay'));

            $signedBalance = ($currentType === 'pay')
                ? -$currentBalance
                : $currentBalance;

            // Payment Out + Discount reduces payable (or increases receivable)
            $newSignedBalance = round($signedBalance + $amount + $discount, 2);

            if ($newSignedBalance < -0.001) {
                $party->opening_balance = abs($newSignedBalance);
                $party->opening_balance_type = 'pay';
            } elseif ($newSignedBalance > 0.001) {
                $party->opening_balance = $newSignedBalance;
                $party->opening_balance_type = 'receive';
            } else {
                $party->opening_balance = 0;
            }

            $party->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment Out saved successfully.',
                'payment_number' => $payment->payment_number,
                'data' => $payment->load([
                    'party',
                    'allocations.purchase',
                ]),
            ], 201);
        });
    }

    // GET: Payment Out history
    public function index(Request $request)
    {
        $payments = PaymentOut::where('user_id', $request->user()->id)
            ->with('party')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    // GET: Single Payment Out details
    public function show(Request $request, $id)
    {
        $payment = PaymentOut::where('user_id', $request->user()->id)
            ->with([
                'party',
                'allocations.purchase',
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }

    // GET: Current balance of selected party
    public function partyBalance(Request $request)
    {
        $request->validate([
            'party_id' => 'required|integer',
        ]);

        $user = $request->user();

        $party = Party::where('user_id', $user->id)
            ->findOrFail($request->party_id);

        $balance = round((float) $party->opening_balance, 2);

        $balanceType = strtolower(
            trim($party->opening_balance_type ?? '')
        );

        return response()->json([
            'success' => true,
            'data' => [
                'party_id' => $party->id,
                'current_balance' => abs($balance),
                'balance_type' => $balanceType === 'pay'
                    ? 'payable'
                    : 'receivable',
            ],
        ]);
    }
}
