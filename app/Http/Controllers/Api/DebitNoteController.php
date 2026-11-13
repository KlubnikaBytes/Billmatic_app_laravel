<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DebitNote;
use App\Models\Purchase;
use App\Models\Party;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DebitNoteController extends Controller
{
    public function nextNumber(Request $request)
    {
        $numbers = DebitNote::where('user_id', $request->user()->id)
            ->pluck('debit_note_number');

        $max = 0;
        foreach ($numbers as $number) {
            if (ctype_digit((string) $number)) {
                $max = max($max, (int) $number);
            }
        }

        return response()->json([
            'success' => true,
            'next_number' => (string) ($max + 1),
        ]);
    }

    public function purchases(Request $request)
    {
        $query = Purchase::where('user_id', $request->user()->id)
            ->with('party');

        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        if ($request->filled('search')) {
            $query->where('purchase_number', 'like',
                '%' . $request->search . '%');
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderByDesc('purchase_date')
                ->orderByDesc('id')
                ->paginate(30),
        ]);
    }

    public function purchaseDetails(Request $request, $id)
    {
        $purchase = Purchase::where('user_id', $request->user()->id)
            ->with(['party', 'items', 'additionalCharges'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $purchase,
        ]);
    }

    public function index(Request $request)
    {
        return DebitNote::where('user_id', $request->user()->id)
            ->with('party')
            ->latest()
            ->paginate(30);
    }

    public function show(Request $request, $id)
    {
        $note = DebitNote::where('user_id', $request->user()->id)
            ->with(['party', 'purchase', 'items', 'additionalCharges'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $note,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'debit_note_number' => 'required|string|max:100',
            'debit_note_date' => 'required|date',
            'party_id' => 'required|integer',
            'purchase_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:5000',

            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|integer',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.unit' => 'nullable|string|max:40',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|between:0,100',

            'additional_charges' => 'nullable|array',
            'additional_charges.*.name' => 'required|string|max:255',
            'additional_charges.*.amount' => 'required|numeric|min:0',

            'discount_percent' => 'nullable|numeric|between:0,100',
            'round_off' => 'nullable|numeric|between:-1000000,1000000',
            'tcs_amount' => 'nullable|numeric|min:0',
            'tds_amount' => 'nullable|numeric|min:0',
            'received_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:100',
        ]);

        $uid = $request->user()->id;

        if (!Party::where('user_id', $uid)
            ->whereKey($data['party_id'])->exists()) {
            throw ValidationException::withMessages([
                'party_id' => 'Party not found.',
            ]);
        }

        if (!empty($data['purchase_id'])) {
            $purchase = Purchase::where('user_id', $uid)
                ->findOrFail($data['purchase_id']);

            if ((int) $purchase->party_id !== (int) $data['party_id']) {
                throw ValidationException::withMessages([
                    'purchase_id' => 'Purchase belongs to another party.',
                ]);
            }
        }

        if (DebitNote::where('user_id', $uid)
            ->where('debit_note_number', $data['debit_note_number'])
            ->exists()) {
            throw ValidationException::withMessages([
                'debit_note_number' => 'Debit Note number already exists.',
            ]);
        }

        $lines = [];
        $subtotal = 0;
        $taxTotal = 0;

        foreach ($data['items'] as $line) {
            if (!empty($line['item_id']) &&
                !Item::where('user_id', $uid)
                    ->whereKey($line['item_id'])->exists()) {
                throw ValidationException::withMessages([
                    'items' => 'Invalid item selected.',
                ]);
            }

            $base = round($line['qty'] * $line['price'], 2);
            $discount = round($line['discount'] ?? 0, 2);

            if ($discount > $base) {
                throw ValidationException::withMessages([
                    'items' => 'Item discount exceeds item amount.',
                ]);
            }

            $taxable = $base - $discount;
            $gstPercent = $line['gst_percent'] ?? 0;
            $gst = round($taxable * $gstPercent / 100, 2);

            $subtotal += $taxable;
            $taxTotal += $gst;

            $lines[] = [
                'item_id' => $line['item_id'] ?? null,
                'description' => $line['description'] ?? null,
                'qty' => $line['qty'],
                'unit' => $line['unit'] ?? null,
                'price' => $line['price'],
                'discount' => $discount,
                'gst_percent' => $gstPercent,
                'gst_amount' => $gst,
                'line_total' => round($taxable + $gst, 2),
            ];
        }

        $charges = collect($data['additional_charges'] ?? []);
        $chargesTotal = round($charges->sum('amount'), 2);

        $subtotal = round($subtotal, 2);
        $taxTotal = round($taxTotal, 2);

        $beforeDiscount = round(
            $subtotal + $taxTotal + $chargesTotal, 2
        );

        $discountPercent = $data['discount_percent'] ?? 0;
        $discountAmount = round(
            $beforeDiscount * $discountPercent / 100, 2
        );

        $roundOff = $data['round_off'] ?? 0;
        $tcs = $data['tcs_amount'] ?? 0;
        $tds = $data['tds_amount'] ?? 0;
        $received = $data['received_amount'] ?? 0;

        $grandTotal = round(
            $beforeDiscount - $discountAmount + $roundOff + $tcs, 2
        );

        if ($grandTotal < 0 || $tds + $received > $grandTotal) {
            throw ValidationException::withMessages([
                'received_amount' => 'Invalid total or received amount.',
            ]);
        }

        $balance = round($grandTotal - $tds - $received, 2);

        $note = DB::transaction(function () use (
            $data, $uid, $subtotal, $taxTotal,
            $discountPercent, $discountAmount, $roundOff,
            $tcs, $tds, $received, $balance, $grandTotal,
            $lines, $charges
        ) {
            $note = DebitNote::create([
                'user_id' => $uid,
                'debit_note_number' => $data['debit_note_number'],
                'debit_note_date' => $data['debit_note_date'],
                'party_id' => $data['party_id'],
                'purchase_id' => $data['purchase_id'] ?? null,
                'subtotal' => $subtotal,
                'total_tax' => $taxTotal,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'round_off' => $roundOff,
                'tcs_amount' => $tcs,
                'tds_amount' => $tds,
                'received_amount' => $received,
                'balance_amount' => $balance,
                'payment_mode' => $data['payment_mode'] ?? null,
                'grand_total' => $grandTotal,
                'notes' => $data['notes'] ?? null,
            ]);

            $note->items()->createMany($lines);
            $note->additionalCharges()->createMany($charges->all());

            return $note;
        });

        return response()->json([
            'success' => true,
            'data' => $note->load([
                'party', 'purchase', 'items', 'additionalCharges'
            ]),
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $note = DebitNote::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $note->delete();

        return response()->json(['success' => true]);
    }
}
