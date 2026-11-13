<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomatedBill;
use App\Models\Item;
use App\Models\Party;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AutomatedBillController extends Controller
{
    private function ownerId(Request $request): int
    {
        return (int) $request->user()->id;
    }

    private function findBill(Request $request, int $id): AutomatedBill
    {
        return AutomatedBill::where('user_id', $this->ownerId($request))
            ->findOrFail($id);
    }

    public function index(Request $request)
    {
        $query = AutomatedBill::with('party:id,party_name')
            ->where('user_id', $this->ownerId($request))
            ->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $request->validate([
                'status' => Rule::in(['active', 'paused', 'stopped', 'all']),
            ]);

            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $bill = $this->findBill($request, $id);

        return response()->json([
            'success' => true,
            'data' => $bill->load([
                'party',
                'items.item',
                'additionalCharges',
                'runs',
            ]),
        ]);
    }

    private function validateBill(Request $request): array
    {
        return $request->validate([
            'party_id' => ['required', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'repeat_every' => ['required', 'integer', 'min:1', 'max:365'],
            'frequency' => [
                'required',
                Rule::in(['day', 'week', 'month', 'year']),
            ],
            'due_days' => ['required', 'integer', 'min:0', 'max:365'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.gst_percent' => ['nullable', 'numeric', 'between:0,100'],
            'additional_charges' => ['sometimes', 'array'],
            'additional_charges.*.name' => ['required', 'string', 'max:255'],
            'additional_charges.*.amount' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'between:0,100'],
            'round_off' => ['nullable', 'numeric'],
            'tcs_amount' => ['nullable', 'numeric', 'min:0'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_mode' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'send_sms' => ['sometimes', 'boolean'],
        ]);
    }

    private function saveDetails(
        AutomatedBill $bill,
        array $data,
        int $ownerId
    ): void {
        $bill->items()->delete();
        $bill->additionalCharges()->delete();

        $subtotal = 0;
        $totalTax = 0;

        foreach ($data['items'] as $row) {
            $item = Item::where('user_id', $ownerId)
                ->findOrFail($row['item_id']);

            $qty = (float) $row['qty'];
            $price = (float) $row['price'];
            $discount = (float) ($row['discount'] ?? 0);
            $gstPercent = (float) ($row['gst_percent'] ?? 0);

            $base = $qty * $price;

            if ($discount > $base) {
                throw ValidationException::withMessages([
                    'items' => 'Item discount cannot exceed item amount.',
                ]);
            }

            $taxable = $base - $discount;
            $tax = round($taxable * $gstPercent / 100, 2);
            $lineTotal = round($taxable + $tax, 2);

            $bill->items()->create([
                'item_id' => $item->id,
                'description' => $row['description'] ?? null,
                'qty' => $qty,
                'unit' => $row['unit'] ?? 'PCS',
                'price' => $price,
                'discount' => $discount,
                'gst_percent' => $gstPercent,
                'gst_amount' => $tax,
                'line_total' => $lineTotal,
            ]);

            $subtotal += $taxable;
            $totalTax += $tax;
        }

        $chargesTotal = 0;

        foreach ($data['additional_charges'] ?? [] as $charge) {
            $amount = (float) $charge['amount'];

            $bill->additionalCharges()->create([
                'name' => $charge['name'],
                'amount' => $amount,
            ]);

            $chargesTotal += $amount;
        }

        $discountPercent = (float) ($data['discount_percent'] ?? 0);
        $discountAmount = round(($subtotal + $totalTax + $chargesTotal) * $discountPercent / 100,2);
        $roundOff = (float) ($data['round_off'] ?? 0);
        $tcsAmount = (float) ($data['tcs_amount'] ?? 0);

        $grandTotal = round(
            $subtotal + $totalTax + $chargesTotal
            - $discountAmount + $roundOff + $tcsAmount,
            2
        );

        if ($grandTotal < 0) {
            throw ValidationException::withMessages([
                'round_off' => 'Total amount cannot be negative.',
            ]);
        }

        $receivedAmount = (float) ($data['received_amount'] ?? 0);

        if ($receivedAmount > $grandTotal) {
            throw ValidationException::withMessages([
                'received_amount' => 'Received amount exceeds total amount.',
            ]);
        }

        $bill->update([
            'subtotal' => round($subtotal, 2),
            'total_tax' => round($totalTax, 2),
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'round_off' => $roundOff,
            'tcs_amount' => $tcsAmount,
            'grand_total' => $grandTotal,
            'received_amount' => $receivedAmount,
            'payment_mode' => $data['payment_mode'] ?? null,
            'notes' => $data['notes'] ?? null,
            'send_sms' => $data['send_sms'] ?? false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateBill($request);
        $ownerId = $this->ownerId($request);

        Party::where('user_id', $ownerId)
            ->findOrFail($data['party_id']);

        $bill = DB::transaction(function () use ($data, $ownerId) {
            $bill = AutomatedBill::create([
                'user_id' => $ownerId,
                'party_id' => $data['party_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'repeat_every' => $data['repeat_every'],
                'frequency' => $data['frequency'],
                'due_days' => $data['due_days'],
                'next_billing_date' => $data['start_date'],
                'status' => 'active',
            ]);

            $this->saveDetails($bill, $data, $ownerId);

            return $bill;
        });

        return response()->json([
            'success' => true,
            'message' => 'Automated bill scheduled successfully.',
            'data' => $bill->load(['party', 'items', 'additionalCharges']),
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $bill = $this->findBill($request, $id);
        $data = $this->validateBill($request);
        $ownerId = $this->ownerId($request);

        Party::where('user_id', $ownerId)
            ->findOrFail($data['party_id']);

        DB::transaction(function () use ($bill, $data, $ownerId) {
            $start = Carbon::parse($data['start_date']);
            $next = $bill->next_billing_date;

            if (!$next || $start->gt($next)) {
                $next = $start;
            }

            $bill->update([
                'party_id' => $data['party_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'repeat_every' => $data['repeat_every'],
                'frequency' => $data['frequency'],
                'due_days' => $data['due_days'],
                'next_billing_date' => $next,
            ]);

            $this->saveDetails($bill, $data, $ownerId);
        });

        return response()->json([
            'success' => true,
            'data' => $bill->fresh()->load([
                'party',
                'items',
                'additionalCharges',
            ]),
        ]);
    }

    public function pause(Request $request, int $id)
    {
        $bill = $this->findBill($request, $id);

        if ($bill->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Only active bills can be paused.',
            ], 422);
        }

        $bill->update(['status' => 'paused']);

        return response()->json([
            'success' => true,
            'message' => 'Automated bill paused.',
            'data' => $bill->fresh(),
        ]);
    }

    public function resume(Request $request, int $id)
    {
        $bill = $this->findBill($request, $id);

        if ($bill->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'Only paused bills can be resumed.',
            ], 422);
        }

        if ($bill->next_billing_date->gt($bill->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Billing schedule has ended.',
            ], 422);
        }

        $bill->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Automated bill resumed.',
            'data' => $bill->fresh(),
        ]);
    }

    public function stop(Request $request, int $id)
    {
        $bill = $this->findBill($request, $id);

        if ($bill->status === 'stopped') {
            return response()->json([
                'success' => false,
                'message' => 'Bill is already stopped.',
            ], 422);
        }

        $bill->update(['status' => 'stopped']);

        return response()->json([
            'success' => true,
            'message' => 'Automated bill stopped.',
            'data' => $bill->fresh(),
        ]);
    }
}
