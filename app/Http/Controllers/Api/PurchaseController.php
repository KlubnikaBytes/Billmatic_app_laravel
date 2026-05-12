<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseAdditionalCharge;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceAdditionalCharge;
use App\Models\Item;
use App\Models\Party;
use Carbon\Carbon;
use App\Services\PaymentLinkService;
use App\Services\SmsService;
use App\Models\BusinessDetail;
//need to dd inn server
use Barryvdh\DomPDF\Facade\Pdf;


class PurchaseController extends Controller
{

public function store(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        // 'purchase_number' => 'required|string|unique:purchases,purchase_number',
        'purchase_number' => 'nullable|string',
        'purchase_date'   => 'required|date',
        'due_date'        => 'nullable|date',
        'place_of_supply' => 'nullable|string|max:100',
        'party_id'        => 'required|exists:parties,id',
        'notes'           => 'nullable|string',

        // ✅ ADDITIONAL CHARGES
        'additional_charges' => 'nullable|array',
        'additional_charges.*.name'   => 'required|string',
        'additional_charges.*.amount' => 'required|numeric|min:0',

        // ✅ DISCOUNT
        'discount_percent' => 'nullable|numeric|min:0|max:100',
        'discount_amount'  => 'nullable|numeric|min:0',

        'round_off'  => 'nullable|numeric',
        'tcs_amount' => 'nullable|numeric|min:0',

        // ✅ PAYMENT
        'received_amount' => 'nullable|numeric|min:0',
        'payment_mode'    => 'nullable|string',

        // ✅ ITEMS
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:items,id',
        // 'items.*.item_id' => 'nullable|exists:items,id',
        // 'items.*.item_id' => 'sometimes|nullable|exists:items,id',
        // 'items.*.item_id' => 'nullable|integer|min:1',
        // 'items.*.hsn' => 'nullable|string',
        'items.*.description' => 'nullable|string',
        'items.*.qty' => 'required|numeric|min:0.01',
        'items.*.unit' => 'nullable|string',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0',
        'items.*.gst_percent' => 'nullable|numeric|min:0',
    ]);

    return DB::transaction(function () use ($data, $user) {

    $lastPurchase = Purchase::where('user_id', $user->id)
    ->where('purchase_number', 'LIKE', 'PUR-%')
    ->latest('id')
    ->first();

    $nextNumber = 1;

    if (
    $lastPurchase &&
    !empty($lastPurchase->purchase_number) &&
    preg_match('/PUR-(\d+)/', $lastPurchase->purchase_number, $match)
    ) {
    $nextNumber = ((int) $match[1]) + 1;
    }

    $purchaseNumber = 'PUR-' . $nextNumber;

        $subtotal = 0;
        $totalTax = 0;

        $preparedItems = [];

        foreach ($data['items'] as $line) {

            $item = Item::lockForUpdate()->findOrFail($line['item_id']);


            // ✅ PURCHASE → ADD STOCK
            $item->opening_stock += $line['qty'];
            $item->save();

            $qty   = $line['qty'];
            $price = $line['price'];
            $disc  = $line['discount'] ?? 0;
            $gst   = $line['gst_percent'] ?? 0;

            $lineAmount = ($qty * $price) - $disc;
            $gstAmount  = $lineAmount * ($gst / 100);

            $subtotal += $lineAmount;
            $totalTax += $gstAmount;

            $preparedItems[] = [
                'item_id'     => $line['item_id'],
                // 'item_id' => $item->id,
                'description' => $line['description'] ?? null,
                'qty'         => $qty,
                'unit'        => $line['unit'] ?? 'PCS',
                'price'       => $price,
                'discount'    => $disc,
                'gst_percent' => $gst,
                'gst_amount'  => $gstAmount,
                'line_total'  => $lineAmount + $gstAmount,
            ];
        }

        // ✅ ADDITIONAL CHARGES
        $additionalTotal = collect($data['additional_charges'] ?? [])
            ->sum('amount');

        // ✅ DISCOUNT
        $discountPercent = $data['discount_percent'] ?? 0;
        $discountAmount  = $data['discount_amount'] ?? 0;

       if ($discountPercent > 0) {
    $discountAmount = (($subtotal + $additionalTotal) * $discountPercent) / 100;
}

        $roundOff = $data['round_off'] ?? 0;
        $tcs      = $data['tcs_amount'] ?? 0;

        // ✅ GRAND TOTAL
        $grandTotal = $subtotal
            + $totalTax
            + $additionalTotal
            - $discountAmount
            + $roundOff
            + $tcs;

        $grandTotal = round($grandTotal, 2);

        // ✅ PAYMENT
        $receivedAmount = round($data['received_amount'] ?? 0, 2);
        $balanceAmount  = round($grandTotal - $receivedAmount, 2);

        if (abs($balanceAmount) < 0.01) {
            $balanceAmount = 0;
        }

        // ✅ STATUS
        if ($balanceAmount <= 0) {
            $status = 'paid';
        } elseif ($receivedAmount <= 0) {
            $status = 'unpaid';
        } else {
            $status = 'partial';
        }

        // ✅ CREATE PURCHASE
        $purchase = Purchase::create([
            'user_id'        => $user->id,
            // 'purchase_number'=> $data['purchase_number'],
            'purchase_number'=> $purchaseNumber,
            'purchase_date'  => $data['purchase_date'],
            'due_date'       => $data['due_date'] ?? null,
            'place_of_supply' => $data['place_of_supply'] ?? null,
            'party_id'       => $data['party_id'],

            'subtotal'       => $subtotal,
            'total_tax'      => $totalTax,

            'discount_percent'=> $discountPercent,
            'discount_amount' => $discountAmount,
            'round_off'       => $roundOff,
            'tcs_amount'      => $tcs,

            'received_amount' => $receivedAmount,
            'balance_amount'  => $balanceAmount,
            'payment_mode'    => $data['payment_mode'] ?? null,
            'status'          => $status,

            'grand_total'     => $grandTotal,
            'notes'           => $data['notes'] ?? null,
        ]);

        // ✅ SAVE ITEMS
        foreach ($preparedItems as $line) {
            $line['purchase_id'] = $purchase->id;
            PurchaseItem::create($line);
        }

        // ✅ SAVE ADDITIONAL CHARGES
        if (!empty($data['additional_charges'])) {
            foreach ($data['additional_charges'] as $charge) {
                PurchaseAdditionalCharge::create([
                    'purchase_id' => $purchase->id,
                    'name'        => $charge['name'],
                    'amount'      => $charge['amount'],
                ]);
            }
        }

        $business = BusinessDetail::where('user_id', $user->id)->first();

        // ✅ RESPONSE (IMPORTANT FOR FLUTTER PREVIEW)
        return response()->json([
            'success' => true,
            'message' => 'Purchase created successfully',
            'data' => [
                'id' => $purchase->id, // ✅ ADD THIS

                 // ✅ ADD THIS
            'business' => [
             'name' => $business->industry ?? '',
             'gstin' => $business->gst_number ?? '',
             'address' => $business->city ?? '',
             'mobile' => $user->mobile ?? '',
              ],
               'place_of_supply' => $purchase->place_of_supply,
                'purchase_number' => $purchase->purchase_number,
                'purchase_date'   => $purchase->purchase_date,
                // ✅ ADD THESE
                'subtotal'        => (float) $purchase->subtotal,
                'total_tax'       => (float) $purchase->total_tax,

                'additional_charge_total' => (float) $purchase->additionalCharges->sum('amount'),

                'discount_amount' => (float) $purchase->discount_amount,
                'round_off'       => (float) $purchase->round_off,
                'tcs_amount'      => (float) $purchase->tcs_amount,
                'grand_total'     => (float) $purchase->grand_total,
                'received_amount' => (float) $purchase->received_amount,
                'balance_amount'  => (float) $purchase->balance_amount,
                'status'          => $purchase->status,

                'party' => $purchase->party,

                'items' => $purchase->items()->with('item')->get()->map(function ($row) {
                    return [
                        'item_id'     => $row->item_id,
                        'description' => $row->item->name,
                        'hsn' => $row->item->hsn_code ?? '', // ✅ ADD THIS
                        'qty'         => $row->qty,
                        'price'       => $row->price,
                        'gst_percent' => $row->gst_percent,
                        'gst_amount'  => $row->gst_amount,
                        'line_total'  => $row->line_total,
                    ];
                }),

                'additional_charges' => $purchase->additionalCharges,
            ]
        ], 201);
    });
}



public function scanBill(Request $request)
{
    if (!$request->hasFile('file')) {
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    $file = $request->file('file');
    $path = $file->store('bills', 'public');
    $fullPath = storage_path("app/public/$path");

    // ✅ OCR
    $text = shell_exec("tesseract " . escapeshellarg($fullPath) . " stdout");

    $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));

    $items = [];
    $partyName = null;
    $foundBillSection = false;

    foreach ($lines as $i => $line) {

        $line = trim($line);

        // ================= SKIP NOISE =================
        if (
            strlen($line) < 3 ||
            preg_match('/(create purchase|add items|subtotal|round off|notes|save|amount paid)/i', $line)
        ) {
            continue;
        }

        // ================= PARTY =================
        if (preg_match('/bill from|bill to/i', $line)) {
            for ($j = $i + 1; $j < count($lines); $j++) {
                $next = trim($lines[$j]);

                if (
                    strlen($next) > 3 &&
                    !preg_match('/(mobile|gst|invoice|date|no|qty|rate)/i', $next)
                ) {
                    $partyName = $next;
                    break;
                }
            }
        }

        // fallback party
        if (!$partyName && preg_match('/^[A-Za-z ]{3,}$/', $line)) {
            if (!preg_match('/(purchase|invoice|total|amount|tax|gst)/i', $line)) {
                $partyName = $line;
            }
        }

        // ================= TABLE START =================
        if (preg_match('/item|qty|rate|amount/i', $line)) {
            $foundBillSection = true;
            continue;
        }

        if (!$foundBillSection) continue;

    
// ================= CLEAN =================
$cleanLine = preg_replace('/[^A-Za-z0-9.\s]/', ' ', $line);
$cleanLine = preg_replace('/\s+/', ' ', $cleanLine);

$nextLine = $lines[$i + 1] ?? '';
$nextLineClean = preg_replace('/[^0-9.\s]/', ' ', $nextLine);
$nextLineClean = preg_replace('/\s+/', ' ', $nextLineClean);

// ================= NUMBERS =================
preg_match_all('/\d+(\.\d+)?/', $cleanLine, $nums);
preg_match_all('/\d+(\.\d+)?/', $nextLineClean, $nextNums);

// ================= PARSER =================
if (preg_match('/^\d+\s+([A-Za-z]+)/', $cleanLine, $nameMatch)) {

    $name = ucfirst(strtolower($nameMatch[1]));

    // ✅ FIX INDEXES
    $hsn = $nums[0][1] ?? null;
    $qty = (float) ($nums[0][2] ?? 1);

    
    // ================= CASE 1: NEXT LINE =================
if (count($nextNums[0]) >= 3) {

    /*
    Example OCR:
    10 111.00 248.64

    Here:
    10 = garbage
    111 = rate
    248.64 = total
    */

    $possible = array_map('floatval', $nextNums[0]);

    // ✅ remove tiny garbage values like 10
    $filtered = array_values(array_filter($possible, function ($v) {
        return $v > 20;
    }));

    // ✅ take last 2 numbers
    $rate  = $filtered[count($filtered) - 2] ?? 0;
    $total = $filtered[count($filtered) - 1] ?? 0;

    $tax = $total - ($qty * $rate);
}

// ================= CASE 2 =================
elseif (count($nextNums[0]) >= 2) {

    $rate  = (float) $nextNums[0][0];
    $total = (float) $nextNums[0][1];

    $tax = $total - ($qty * $rate);
}

    // ================= CASE 2: SAME LINE =================
    elseif (count($nums[0]) >= 6) {

        $rate  = (float) $nums[0][3];
        $tax   = (float) $nums[0][4];
        $total = (float) $nums[0][5];
    }

    else {
        continue;
    }

    // ================= CALC =================
    $base = $qty * $rate;

    if ($total < $base) {
        $total = $base + $tax;
    }

    $gst = $base > 0 ? round(($tax / $base) * 100, 2) : 0;

    // ================= FILTER =================
    if ($qty <= 0 || $rate <= 0) continue;

    $items[] = [
        "item_id" => null,
        "description" => $name,
        "hsn" => $hsn,
        "qty" => $qty,
        "price" => $rate,
        "gst_percent" => $gst,
        "gst_amount" => round($tax, 2),
        "line_total" => round($total, 2),
    ];
}
    }

    // ================= REMOVE DUPLICATES =================
    $items = collect($items)
        ->unique(function ($i) {
            return strtolower($i['description']) . '-' . $i['line_total'];
        })
        ->values();

    // ================= VALIDATION =================
    if (count($items) === 0) {
        return response()->json([
            "error" => "No valid items detected. Scan a clear bill.",
            "raw_text" => $text
        ], 422);
    }

    return response()->json([
        "place_of_supply" => "Gujarat",
        "party" => [
            "id" => 1,
            "party_name" => $partyName ?? "Unknown"
        ],
        "items" => $items,
        "raw_text" => $text
    ]);
}

}
