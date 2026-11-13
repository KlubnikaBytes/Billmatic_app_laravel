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

use Illuminate\Support\Facades\Http;
// use App\Services\GoogleDocumentAIService;

use App\Services\OCRSpaceService;


class PurchaseController extends Controller
{


public function store(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        'purchase_number' => 'required|string|unique:purchases,purchase_number',
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

        'items.*.description' => 'nullable|string',
        'items.*.qty' => 'required|numeric|min:0.01',
        'items.*.unit' => 'nullable|string',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0',
        'items.*.gst_percent' => 'nullable|numeric|min:0',
    ]);

    return DB::transaction(function () use ($data, $user) {

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
    // Discount is calculated AFTER Tax + Additional Charges
    $discountBase = $subtotal + $totalTax + $additionalTotal;

    $discountAmount = ($discountBase * $discountPercent) / 100;
}

$discountAmount = round($discountAmount, 2);

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

// If payment equals the displayed/rounded total, balance must be zero
if (abs($grandTotal - $receivedAmount) < 0.50) {
    $balanceAmount = 0;
} else {
    $balanceAmount = round($grandTotal - $receivedAmount, 2);
}

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
            'purchase_number'=> $data['purchase_number'],
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
             'name' => $business->business_name ?? '',
             'gstin' => $business->gstin ?? '',
             'address' => $business->address ?? '',
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

//need to add

public function index(Request $request)
{
    $user = $request->user();

    $query = Purchase::where('user_id', $user->id)
        ->with('party')
        ->orderBy('purchase_date', 'desc')
        ->orderBy('id', 'desc');

    $purchases = $query->get();

    return response()->json([
        'success' => true,

        'data' => [
            'total_purchases' => round(
                $purchases->sum('grand_total'),
                2
            ),

            'paid' => round(
                $purchases
                    ->where('status', 'paid')
                    ->sum('grand_total'),
                2
            ),

            'unpaid' => round(
                $purchases
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->sum('balance_amount'),
                2
            ),

            'purchases' => $purchases->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'purchase_number' => $purchase->purchase_number,
                    'purchase_date' => $purchase->purchase_date,
                    'due_date' => $purchase->due_date,

                    'party_name' =>
                        $purchase->party->party_name ?? 'Cash Purchase',

                    'amount' =>
                        (float) $purchase->grand_total,

                    'received_amount' =>
                        (float) $purchase->received_amount,

                    'balance_amount' =>
                        (float) $purchase->balance_amount,

                    'status' =>
                        $purchase->status,
                ];
            })->values(),
        ],
    ]);
}

public function show($id)
{
    $purchase = Purchase::with([
        'party',
        'items.item',
        'additionalCharges'
    ])->findOrFail($id);

    $business = BusinessDetail::where(
        'user_id',
        $purchase->user_id
    )->first();

    return response()->json([
        'success' => true,

        'data' => [

            'id' => $purchase->id,

            'purchase_number' => $purchase->purchase_number,

            'purchase_date' => $purchase->purchase_date,

            'due_date' => $purchase->due_date,

            'place_of_supply' => $purchase->place_of_supply,

            'status' => $purchase->status,

            // ✅ IMPORTANT
            'subtotal' => (float) $purchase->subtotal,

            'total_tax' => (float) $purchase->total_tax,

            'grand_total' => (float) $purchase->grand_total,

            'balance_amount' => (float) $purchase->balance_amount,

            'received_amount' => (float) $purchase->received_amount,

            'payment_mode' => $purchase->payment_mode,

            'notes' => $purchase->notes,

            // ✅ BUSINESS
            'business' => [
                'name' => $business->business_name ?? '',
                'gstin' => $business->gstin ?? '',
                'address' => $business->address ?? '',
                'mobile' => $purchase->user->mobile ?? '',
            ],

            // ✅ PARTY
            'party' => [
                'party_name' => $purchase->party->party_name ?? '',
                'contact_number' => $purchase->party->contact_number ?? '',
            ],

            // ✅ ITEMS
            'items' => $purchase->items->map(function ($item) {

                return [

                    'item_id' => $item->item_id,

                    // ✅ IMPORTANT FIX
                    'description' =>
                        $item->description
                        ?? $item->item->name
                        ?? '',

                    // ✅ IMPORTANT FIX
                    'hsn' =>
                        $item->hsn
                        ?? $item->item->hsn_code
                        ?? '',

                    'qty' => $item->qty,

                    'price' => $item->price,

                    'gst_percent' => $item->gst_percent,

                    'gst_amount' => $item->gst_amount,

                    'line_total' => $item->line_total,
                ];
            }),

            'additional_charges' => $purchase->additionalCharges,
        ]
    ]);
}

//need//
public function gstPurchaseReport(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        'from' => 'required|date',
        'to'   => 'required|date|after_or_equal:from',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $user->id
    )->first();

    $purchases = Purchase::where('user_id', $user->id)
        ->whereDate('purchase_date', '>=', $data['from'])
        ->whereDate('purchase_date', '<=', $data['to'])
        ->with(['party', 'items.item'])
        ->orderBy('purchase_date')
        ->orderBy('id')
        ->get();

    return response()->json([
        'success' => true,

        'business' => [
            'name' => $business->business_name ?? '',
            'mobile' => $user->mobile ?? '',
            'gstin' => $business->gstin ?? '',
        ],

        'purchases' => $purchases->map(function ($purchase) {
            return [
                'id' => $purchase->id,
                'invoice_number' => $purchase->purchase_number,
                'invoice_date' => $purchase->purchase_date,
                'invoice_value' => (float) $purchase->grand_total,
                'taxable_value' => (float) $purchase->subtotal,
                'total_tax' => (float) $purchase->total_tax,
                'place_of_supply' => $purchase->place_of_supply,

                'supplier' => [
                    'name' => $purchase->party->party_name ?? '',
                    'gstin' => $purchase->party->gstin ?? '',
                    'mobile' => $purchase->party->contact_number ?? '',
                ],

                'items' => $purchase->items->map(function ($item) {
                    return [
                        'name' => $item->description
                            ?? $item->item->name
                            ?? '',
                        'hsn' => $item->hsn
                            ?? $item->item->hsn_code
                            ?? '',
                        'quantity' => (float) $item->qty,
                        'price' => (float) $item->price,
                        'gst_percent' => (float) $item->gst_percent,
                        'gst_amount' => (float) $item->gst_amount,
                        'line_total' => (float) $item->line_total,
                    ];
                })->values(),
            ];
        })->values(),
    ]);
}

public function preview($id)
{
    $purchase = Purchase::with([
        'party',
        'items.item',
        'additionalCharges'
    ])->findOrFail($id);

    $business = BusinessDetail::where(
        'user_id',
        $purchase->user_id
    )->first();

    return view('purchase-preview', [
        'purchase' => $purchase,
        'business' => $business,
    ]);
}

public function scanBill(
    Request $request,
    OCRSpaceService $ocr
) {
    // =====================================================
    // 1. CHECK FILE
    // =====================================================

    if (!$request->hasFile('file')) {
        return response()->json([
            'error' => 'No file uploaded'
        ], 400);
    }

    $file = $request->file('file');

    // =====================================================
    // 2. VALIDATE FILE
    // =====================================================

    $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/webp',
        'application/pdf',
    ];

    if (!in_array($file->getMimeType(), $allowedMimes)) {
        return response()->json([
            'error' => 'Only JPG, PNG, WEBP and PDF files are supported.'
        ], 422);
    }

    // =====================================================
    // 3. OCR
    // =====================================================

    try {

        $ocrResult = $ocr->processInvoice(
            $file->getRealPath(),
            $file->getClientOriginalName()
        );

        $text = $ocrResult['text'] ?? '';

        if (trim($text) === '') {
            return response()->json([
                'error' => 'OCR returned empty text.'
            ], 422);
        }

    } catch (\Throwable $e) {

        return response()->json([
            'error' => 'OCR processing failed',
            'message' => $e->getMessage(),
        ], 500);
    }

    // =====================================================
    // 4. NORMALIZE OCR TEXT
    // =====================================================

    $rawText = $text;

    // Normalize different OCR symbols
    $text = str_replace(
        [
            "\u{00A0}",
            "\u{2022}",
            "\u{00B7}",
            "₹",
            "â‚¹",
        ],
        [
            " ",
            " ",
            " ",
            " INR ",
            " INR ",
        ],
        $text
    );

   // =====================================================
// NORMALIZE OCR LINES
// IMPORTANT: KEEP TABS
// =====================================================

$rawText = $text;

// Normalize line endings only
$text = str_replace(
    ["\r\n", "\r"],
    "\n",
    $text
);

// Normalize NBSP
$text = str_replace(
    "\u{00A0}",
    ' ',
    $text
);

$lines = explode(
    "\n",
    $text
);

$lines = array_values(
    array_filter(
        array_map(
            function ($line) {

                $line = trim($line);

                // DO NOT REMOVE TABS HERE.
                // Tabs contain OCR column information.

                return $line;

            },
            $lines
        ),
        function ($line) {

            return trim($line) !== '';
        }
    )
);

    // =====================================================
    // HELPER: NORMALIZE LINE
    // =====================================================

    $normalize = function ($value) {

        $value = strtolower(
            trim((string) $value)
        );

        $value = preg_replace(
            '/\s+/',
            ' ',
            $value
        );

        return trim($value);
    };

    // =====================================================
    // HELPER: EXTRACT NUMBERS
    // =====================================================

    $extractNumbers = function ($value) {

        preg_match_all(
            '/-?\d+(?:[.,]\d+)?/',
            $value,
            $matches
        );

        return array_map(
            function ($number) {

                return (float) str_replace(
                    ',',
                    '',
                    $number
                );

            },
            $matches[0]
        );
    };

    // =====================================================
    // HELPER: CLEAN ITEM NAME
    // =====================================================

    $cleanItemName = function ($value) {

        $value = trim($value);

        // Remove serial number
        $value = preg_replace(
            '/^\d+\s*/',
            '',
            $value
        );

        // Remove currency symbols
        $value = preg_replace(
            '/[$₹€£]/u',
            ' ',
            $value
        );

        // Remove common OCR garbage
        $value = preg_replace(
            '/[·•|]+/u',
            ' ',
            $value
        );

        // Normalize spaces
        $value = preg_replace(
            '/\s+/',
            ' ',
            $value
        );

        return trim(
            $value,
            " .,:;|_-"
        );
    };

    // =====================================================
    // 5. FIND PARTY
    // =====================================================

    $partyName = null;

    foreach ($lines as $i => $line) {

        $normalized = $normalize($line);

        if (
            preg_match(
                '/^bill\s*from$/i',
                $normalized
            )
        ) {

            // Look at next few lines
            for (
                $j = $i + 1;
                $j < min($i + 5, count($lines));
                $j++
            ) {

                $candidate = trim($lines[$j]);

                if ($candidate === '') {
                    continue;
                }

                if (
                    preg_match(
                        '/mobile|phone|gstin|gst|pan|place\s+of\s+supply|invoice|purchase|date/i',
                        $candidate
                    )
                ) {
                    continue;
                }

                // Avoid pure numbers
                if (
                    preg_match(
                        '/^\d+$/',
                        $candidate
                    )
                ) {
                    continue;
                }

                $partyName = $candidate;

                break;
            }

            if ($partyName !== null) {
                break;
            }
        }
    }

    // =====================================================
    // FALLBACK PARTY DETECTION
    // =====================================================

    if (!$partyName) {

        foreach ($lines as $line) {

            if (
                preg_match(
                    '/^(central|suraj|vendor|supplier|m\/s|ms\.?)\b/i',
                    trim($line)
                )
            ) {

                $candidate = trim($line);

                if (
                    !preg_match(
                        '/gstin|mobile|phone|invoice|purchase/i',
                        $candidate
                    )
                ) {

                    $partyName = $candidate;

                    break;
                }
            }
        }
    }

   // =====================================================
// 6. PLACE OF SUPPLY
// =====================================================

$placeOfSupply = null;

foreach ($lines as $line) {

    $cleanLine = trim($line);

    // -------------------------------------------------
    // Format:
    // Place of Supply: Gujarat
    // -------------------------------------------------

    if (
        preg_match(
            '/^place\s+of\s+supply\s*:\s*(.+)$/i',
            $cleanLine,
            $match
        )
    ) {

        $value = trim($match[1]);

        if (
            $value !== '' &&
            $value !== ':'
        ) {

            $placeOfSupply = $value;

            break;
        }
    }

    // -------------------------------------------------
    // Format:
    // Place of Supply Gujarat
    // -------------------------------------------------

    if (
        preg_match(
            '/^place\s+of\s+supply\s+(.+)$/i',
            $cleanLine,
            $match
        )
    ) {

        $value = trim($match[1]);

        if (
            $value !== '' &&
            $value !== ':'
        ) {

            $placeOfSupply = $value;

            break;
        }
    }
}

    // =====================================================
// 7. FIND PURCHASE ITEM TABLE
// =====================================================

$tableStart = -1;
$tableEnd = count($lines);

// =====================================================
// TABLE HEADER DETECTION
// =====================================================

foreach ($lines as $i => $line) {

    $normalized =
        strtolower(
            preg_replace(
                '/\s+/',
                ' ',
                str_replace(
                    "\t",
                    ' ',
                    trim($line)
                )
            )
        );

    $hasItem =
        preg_match(
            '/\bitem\b|\bitems\b|\bproduct\b|\bdescription\b|\bparticular\b/i',
            $normalized
        );

    $hasHsn =
        preg_match(
            '/\bhsn\b|\bhsn\s*no\b|\bh\.?s\.?n\b/i',
            $normalized
        );

    $hasQty =
        preg_match(
            '/\bqty\b|\bquantity\b|\bpcs\b|\bunits?\b/i',
            $normalized
        );

    $hasRate =
        preg_match(
            '/\brate\b|\bprice\b/i',
            $normalized
        );

    $hasTotal =
        preg_match(
            '/\btotal\b|\bamount\b/i',
            $normalized
        );

    /*
     * Normal invoice header
     *
     * No Item HSN Qty Rate Tax Total
     *
     * OR
     *
     * Items HSN Qty Rate Tax Total
     */

    if (
        (
            $hasItem &&
            $hasHsn &&
            $hasQty
        )
        ||
        (
            $hasHsn &&
            $hasQty &&
            $hasTotal
        )
    ) {

        $tableStart = $i + 1;

        break;
    }
}

// =====================================================
// SECOND FALLBACK:
// Some OCR loses "Item / HSN" header completely.
//
// Example:
//
// Qty. Tax Total
// coal 2356 2 PCS 180 378
// mango 5959 150 150
// =====================================================

if ($tableStart < 0) {

    foreach ($lines as $i => $line) {

        $normalized =
            strtolower(
                preg_replace(
                    '/\s+/',
                    ' ',
                    str_replace(
                        "\t",
                        ' ',
                        trim($line)
                    )
                )
            );

       if (
    preg_match('/\bqty\b|\bquantity\b/i', $normalized) &&
    preg_match('/\btax\b/i', $normalized) &&
    preg_match('/\btotal\b|\btatal\b|\bamount\b/i', $normalized)
) {

    $tableStart =
        $i + 1;

    break;
}
    }
}

// =====================================================
// IF TABLE WAS FOUND, FIND END
// =====================================================

if ($tableStart >= 0) {

    for (
        $i = $tableStart;
        $i < count($lines);
        $i++
    ) {

        $normalized =
            strtolower(
                preg_replace(
                    '/\s+/',
                    ' ',
                    str_replace(
                        "\t",
                        ' ',
                        trim($lines[$i])
                    )
                )
            );

        if (
            preg_match(
                '/^(subtotal|sub total|taxable amount|service tax|additional charges?|discount|round off|cgst|sgst|igst|total amount|grand total|paid amount|paid|balance|terms|terms & conditions)/i',
                $normalized
            )
        ) {

            $tableEnd = $i;

            break;
        }
    }
}

// =====================================================
// 8. HELPER FUNCTIONS FOR ITEM TABLE
// =====================================================

$extractNumbers = function ($value) {

    preg_match_all(
        '/-?\d+(?:[.,]\d+)?/',
        $value,
        $matches
    );

    return array_map(
        function ($number) {

            return (float)
                str_replace(
                    ',',
                    '',
                    $number
                );
        },
        $matches[0]
    );
};

$cleanItemName = function ($value) {

    $value = trim($value);

    // Remove serial number
    $value = preg_replace(
        '/^\d+\s*/',
        '',
        $value
    );

    // Remove currency
    $value = preg_replace(
        '/[$₹€£]/u',
        ' ',
        $value
    );

    // OCR separators
    $value = preg_replace(
        '/[·•|]+/u',
        ' ',
        $value
    );

    // Remove common words
    $value = preg_replace(
        '/^(item|items|product|description)\s*/i',
        '',
        $value
    );

    $value = preg_replace(
        '/\s+/',
        ' ',
        $value
    );

    return trim(
        $value,
        " .,:;|-_"
    );
};

// =====================================================
// 9. COMBINE MULTILINE OCR ROWS
// =====================================================

$tableRows = [];

if ($tableStart >= 0) {

    for (
        $i = $tableStart;
        $i < $tableEnd;
        $i++
    ) {

        $line =
            trim(
                $lines[$i]
            );

        if (
            $line === ''
        ) {
            continue;
        }

        // =================================================
        // STOP CONDITIONS
        // =================================================

        $normalized =
            strtolower(
                preg_replace(
                    '/\s+/',
                    ' ',
                    str_replace(
                        "\t",
                        ' ',
                        $line
                    )
                )
            );

        if (
            preg_match(
                '/^(subtotal|sub total|taxable amount|service tax|additional charges?|discount|round off|cgst|sgst|igst|total amount|grand total|paid amount|paid|balance|terms)/i',
                $normalized
            )
        ) {

            break;
        }

        // =================================================
        // SPLIT TAB COLUMNS
        // =================================================

        $columns =
            array_values(
                array_filter(
                    array_map(
                        'trim',
                        explode(
                            "\t",
                            $line
                        )
                    ),
                    function ($value) {

                        return
                            $value !== '';
                    }
                )
            );

        // =================================================
        // DOES THIS LOOK LIKE AN ITEM START?
        // =================================================

        $hasHsn =
            preg_match(
                '/\b\d{4,8}\b/',
                $line
            );

        $hasItemText =
            preg_match(
                '/[A-Za-z]{2,}/',
                $line
            );

        if (
            $hasHsn &&
            $hasItemText
        ) {

            /*
             * Start a new item row.
             */

            $tableRows[] = [
                'line' =>
                    $line,

                'columns' =>
                    $columns,
            ];

            continue;
        }

        // =================================================
        // CONTINUATION LINE
        // =================================================

        /*
         * Example first invoice:
         *
         * 1 banana 5434 3.00 · 54.00
         *
         * next line:
         *
         * 100.00 354.00
         *
         * These belong to banana.
         */

        if (
            !empty($tableRows)
        ) {

            $lastIndex =
                count($tableRows) - 1;

            $tableRows[$lastIndex][
                'line'
            ] .=
                "\t" .
                $line;

            $tableRows[$lastIndex][
                'columns'
            ] = array_merge(
                $tableRows[$lastIndex][
                    'columns'
                ],
                $columns
            );
        }
    }
}

// =====================================================
// 10. PARSE TABLE ROWS
// =====================================================

$items = [];

foreach (
    $tableRows as $row
) {

    $columns =
        $row['columns'];

    $line =
        $row['line'];

    if (
        count($columns) < 3
    ) {
        continue;
    }

    // =================================================
    // FIND HSN COLUMN
    // =================================================

    $hsnIndex = null;
    $hsn = '';

    foreach (
        $columns as $index => $column
    ) {

        /*
         * HSN normally 4-8 digits.
         */

        if (
            preg_match(
                '/^\d{4,8}$/',
                trim($column)
            )
        ) {

            $hsnIndex =
                $index;

            $hsn =
                trim($column);

            break;
        }
    }

    if (
        $hsnIndex === null
    ) {

        /*
         * Try HSN from entire line.
         */

        if (
            !preg_match(
                '/\b(\d{4,8})\b/',
                $line,
                $hsnMatch
            )
        ) {

            continue;
        }

        $hsn =
            $hsnMatch[1];
    }

    // =================================================
    // ITEM NAME
    // =================================================

    if (
        $hsnIndex !== null
    ) {

        /*
         * Everything before HSN is normally:
         *
         * [No] [Item]
         *
         * OR
         *
         * [Item]
         */

        $nameParts = [];

        for (
            $j = 0;
            $j < $hsnIndex;
            $j++
        ) {

            $candidate =
                trim(
                    $columns[$j]
                );

            if (
                $candidate === ''
            ) {
                continue;
            }

            /*
             * Ignore serial number.
             */

            if (
                preg_match(
                    '/^\d+$/',
                    $candidate
                )
            ) {
                continue;
            }

            $nameParts[] =
                $candidate;
        }

        $name =
            $cleanItemName(
                implode(
                    ' ',
                    $nameParts
                )
            );

    } else {

        $beforeHsn =
            substr(
                $line,
                0,
                strpos(
                    $line,
                    $hsn
                )
            );

        $name =
            $cleanItemName(
                $beforeHsn
            );
    }

    if (
        $name === ''
    ) {
        continue;
    }

    // =================================================
    // DATA AFTER HSN
    // =================================================

    $afterColumns = [];

    if (
        $hsnIndex !== null
    ) {

        $afterColumns =
            array_slice(
                $columns,
                $hsnIndex + 1
            );
    }

    // =================================================
    // REMOVE OCR GARBAGE FROM COLUMNS
    // =================================================

    $afterColumns =
        array_values(
            array_filter(
                array_map(
                    function ($value) {

                        return trim(
                            preg_replace(
                                '/[₹$€£·•]/u',
                                ' ',
                                $value
                            )
                        );
                    },
                    $afterColumns
                ),
                function ($value) {

                    return
                        trim($value) !== '';
                }
            )
        );

  // =================================================
// QUANTITY / RATE / TAX / TOTAL
// =================================================

$qty = null;
$rate = 0;
$tax = 0;
$total = 0;

// =================================================
// BUILD NUMERIC COLUMNS AFTER HSN
// =================================================

$parsedColumns = [];

foreach ($afterColumns as $index => $column) {

    $column = trim($column);

    if ($column === '') {
        continue;
    }

    // -------------------------------------------------
    // QUANTITY WITH UNIT
    // -------------------------------------------------

    if (
        preg_match(
            '/^(\d+(?:\.\d+)?)\s*(pcs?|pieces?|nos?|units?|kg|kgs?|g|gm|grams?|ltr|litre|liter|box|bags?|pack)$/i',
            $column,
            $qtyMatch
        )
    ) {

        $parsedColumns[] = [
            'type' => 'qty',
            'value' => (float) $qtyMatch[1],
        ];

        continue;
    }

    // -------------------------------------------------
    // NORMAL NUMERIC COLUMN
    // -------------------------------------------------

    $numbers = $extractNumbers($column);

    foreach ($numbers as $number) {

        $parsedColumns[] = [
            'type' => 'number',
            'value' => $number,
        ];
    }
}

// =================================================
// CASE A:
// EXPLICIT QTY WITH UNIT
//
// ccal | 2356 | 2 PCS | 180 | 378
// =================================================

$explicitQtyIndex = null;

foreach ($parsedColumns as $index => $column) {

    if ($column['type'] === 'qty') {

        $possibleQty = $column['value'];

        if (
            $possibleQty > 0 &&
            $possibleQty <= 10000
        ) {

            $qty = $possibleQty;
            $explicitQtyIndex = $index;

            break;
        }
    }
}

// Remove explicit quantity
if ($explicitQtyIndex !== null) {

    unset($parsedColumns[$explicitQtyIndex]);

    $parsedColumns =
        array_values($parsedColumns);
}

// =================================================
// NUMBERS ONLY
// =================================================

$numericValues = [];

foreach ($parsedColumns as $column) {

    if ($column['type'] === 'number') {

        $numericValues[] =
            (float) $column['value'];
    }
}

// =================================================
// CASE B:
// NO UNIT
//
// banana | 5434 | 3.00 | 54.00 | 100.00 | 354.00
//
// Table structure:
// Qty | Rate | Tax | Total
//
// We use the OCR column order.
// =================================================

if ($qty === null) {

    /*
     * Need at least 4 values:
     *
     * Qty
     * Tax
     * Rate
     * Total
     *
     * OCR may rearrange Rate/Tax visually,
     * so we validate using arithmetic.
     */

    if (count($numericValues) >= 4) {

        foreach (
            $numericValues as $qIndex => $possibleQty
        ) {

            if (
                $possibleQty <= 0 ||
                $possibleQty > 10000
            ) {
                continue;
            }

            for (
                $rIndex = 0;
                $rIndex < count($numericValues);
                $rIndex++
            ) {

                if ($rIndex === $qIndex) {
                    continue;
                }

                $possibleRate =
                    $numericValues[$rIndex];

                if ($possibleRate <= 0) {
                    continue;
                }

                $base =
                    $possibleQty *
                    $possibleRate;

                /*
                 * Look for a value that can be tax
                 * and another value that equals:
                 *
                 * base + tax
                 */

                foreach (
                    $numericValues as $taxIndex => $possibleTax
                ) {

                    if (
                        $taxIndex === $qIndex ||
                        $taxIndex === $rIndex
                    ) {
                        continue;
                    }

                    if ($possibleTax < 0) {
                        continue;
                    }

                    /*
                     * Tax should normally be <= 50%
                     * of the base.
                     */

                    if (
                        $possibleTax >
                        ($base * 0.50)
                    ) {
                        continue;
                    }

                    $expectedTotal =
                        round(
                            $base +
                            $possibleTax,
                            2
                        );

                    foreach (
                        $numericValues as $totalIndex => $possibleTotal
                    ) {

                        if (
                            $totalIndex === $qIndex ||
                            $totalIndex === $rIndex ||
                            $totalIndex === $taxIndex
                        ) {
                            continue;
                        }

                        if (
                            abs(
                                $possibleTotal -
                                $expectedTotal
                            ) < 0.02
                        ) {

                            $qty =
                                $possibleQty;

                            $rate =
                                $possibleRate;

                            $tax =
                                $possibleTax;

                            $total =
                                $possibleTotal;

                            break 4;
                        }
                    }
                }
            }
        }
    }
}

// =================================================
// CASE C:
// NO EXPLICIT QTY AND ONLY TWO NUMBERS
//
// mango | 5959 | 150 | 150
//
// qty = 1
// rate = 150
// total = 150
// tax = 0
// =================================================

if (
    $qty === null
) {

    $qty = 1;

    if (
        count($numericValues) >= 2
    ) {

        $rate =
            $numericValues[
                count($numericValues) - 2
            ];

        $total =
            $numericValues[
                count($numericValues) - 1
            ];

        $base =
            $qty *
            $rate;

        $tax =
            max(
                0,
                round(
                    $total -
                    $base,
                    2
                )
            );
    }
}

// =================================================
// CASE D:
// EXPLICIT QTY WAS FOUND
//
// ccal | 2356 | 2 PCS | 180 | 378
//
// qty = 2
// rate = 180
// total = 378
// tax = 18
// =================================================

if (
    $qty !== null &&
    $rate <= 0 &&
    count($numericValues) >= 2
) {

    $rate =
        $numericValues[
            count($numericValues) - 2
        ];

    $total =
        $numericValues[
            count($numericValues) - 1
        ];

    $base =
        $qty *
        $rate;

    $calculatedTax =
        round(
            $total -
            $base,
            2
        );

    if (
        $calculatedTax >= 0 &&
        $calculatedTax <=
            ($base * 0.50)
    ) {

        $tax =
            $calculatedTax;
    }
}

// =================================================
// SAFETY
// =================================================

if (
    $qty === null ||
    $qty <= 0
) {
    $qty = 1;
}

if (
    $rate <= 0
) {
    continue;
}

if (
    $total <= 0
) {

    $total =
        round(
            ($qty * $rate) +
            $tax,
            2
        );
}

// =================================================
// FINAL BASE
// =================================================

$baseAmount =
    round(
        $qty *
        $rate,
        2
    );

// =================================================
// FINAL TAX
// =================================================

if (
    $total >= $baseAmount
) {

    $calculatedTax =
        round(
            $total -
            $baseAmount,
            2
        );

    if (
        $calculatedTax >= 0 &&
        $calculatedTax <=
            ($baseAmount * 0.50)
    ) {

        $tax =
            $calculatedTax;
    }
}

// =================================================
// GST %
// =================================================

$gstPercent = 0;

if (
    $baseAmount > 0 &&
    $tax > 0
) {

    $gstPercent =
        round(
            (
                $tax /
                $baseAmount
            ) * 100,
            2
        );
}

// =================================================
// ADD ITEM
// =================================================

$items[] = [

    'item_id' =>
        null,

    'description' =>
        $name,

    'hsn' =>
        $hsn,

    'qty' =>
        round(
            $qty,
            3
        ),

    'price' =>
        round(
            $rate,
            2
        ),

    'gst_percent' =>
        round(
            $gstPercent,
            2
        ),

    'gst_amount' =>
        round(
            $tax,
            2
        ),

    'line_total' =>
        round(
            $total,
            2
        ),
];
}

// =====================================================
// 11. REMOVE DUPLICATES
// =====================================================

$items =
    collect($items)
        ->filter(function ($item) {

            return
                trim(
                    $item['description']
                ) !== '' &&
                $item['qty'] > 0 &&
                $item['price'] > 0;
        })
        ->unique(function ($item) {

            return
                strtolower(
                    trim(
                        $item['description']
                    )
                )
                . '|'
                . $item['hsn']
                . '|'
                . $item['qty']
                . '|'
                . $item['price']
                . '|'
                . $item['line_total'];

        })
        ->values()
        ->toArray();

// =====================================================
// 12. VALIDATE
// =====================================================

if (
    count($items) === 0
) {

    return response()->json([

        'error' =>
            'No valid invoice items detected.',

        'message' =>
            'OCR text was received, but no purchase item rows could be identified.',

        'raw_text' =>
            $rawText,

    ], 422);
}

    // =====================================================
    // 12. VALIDATE ITEMS
    // =====================================================

    if (
        count($items) === 0
    ) {

        return response()->json([

            'error' =>
                'No valid invoice items detected.',

            'message' =>
                'The OCR text was read, but the item table could not be identified.',

            'raw_text' =>
                $rawText,

        ], 422);
    }

    // =====================================================
    // 13. CALCULATE ITEM TOTALS
    // =====================================================

    $subtotal = 0;

    $totalTax = 0;

    foreach (
        $items as $item
    ) {

        $subtotal +=
            (
                (float)
                $item['qty']
            ) *
            (
                (float)
                $item['price']
            );

        $totalTax +=
            (float)
            $item['gst_amount'];
    }

    $subtotal =
        round(
            $subtotal,
            2
        );

    $totalTax =
        round(
            $totalTax,
            2
        );

    // =====================================================
    // 14. INVOICE LEVEL TOTALS
    // =====================================================

    $additionalChargeTotal = 0;

    $discountAmount = 0;

    $roundOff = 0;

    $invoiceGrandTotal = null;

    $receivedAmount = null;

    $balanceAmount = null;

    // =====================================================
    // HELPER: LAST MONEY VALUE
    // =====================================================

    $extractLastAmount =
        function ($line) {

            /*
             * Remove percentages from consideration
             */

            $lineWithoutPercent =
                preg_replace(
                    '/\d+(?:\.\d+)?\s*%/',
                    '',
                    $line
                );

            preg_match_all(
                '/-?\d+(?:[.,]\d+)?/',
                $lineWithoutPercent,
                $matches
            );

            if (
                empty(
                    $matches[0]
                )
            ) {
                return null;
            }

            return (float)
                str_replace(
                    ',',
                    '',
                    end(
                        $matches[0]
                    )
                );
        };

   // =====================================================
// 15. READ SUMMARY
// =====================================================

/**
 * Extract amount from a line.
 *
 * Examples:
 * Paid Amount ₹655
 * Paid Amount 655
 * Discount -73
 * Balance ₹0
 */
$extractLastAmount = function ($line) {

    $line = str_replace(
        ["₹", "â‚¹", "INR"],
        " ",
        $line
    );

    // Remove percentages from consideration
    $line = preg_replace(
        '/\d+(?:[.,]\d+)?\s*%/',
        '',
        $line
    );

    preg_match_all(
        '/-?\d+(?:[.,]\d+)?/',
        $line,
        $matches
    );

    if (
        empty($matches[0])
    ) {
        return null;
    }

    $value =
        end($matches[0]);

    return (float) str_replace(
        ',',
        '',
        $value
    );
};


/**
 * Find the next numeric amount after a summary label.
 *
 * Handles OCR such as:
 *
 * Paid Amount
 * 655
 *
 * or:
 *
 * Paid
 * Amount
 * 655
 *
 * or:
 *
 * Paid Amount    ₹655
 */
$extractAmountAfterLabel = function (
    $lines,
    $index,
    $labelPattern,
    $maxLookAhead = 3
) use ($extractLastAmount) {

    // -------------------------------------------------
    // First: check current line
    // -------------------------------------------------

    $currentLine =
        trim($lines[$index]);

    $amount =
        $extractLastAmount(
            $currentLine
        );

    if (
        $amount !== null
    ) {
        return $amount;
    }

    // -------------------------------------------------
    // Look at next few OCR lines
    // -------------------------------------------------

    for (
        $j = $index + 1;
        $j < min(
            $index + 1 + $maxLookAhead,
            count($lines)
        );
        $j++
    ) {

        $nextLine =
            trim($lines[$j]);

        if (
            $nextLine === ''
        ) {
            continue;
        }

        $normalizedNext =
            strtolower(
                preg_replace(
                    '/\s+/',
                    ' ',
                    str_replace(
                        "\t",
                        ' ',
                        $nextLine
                    )
                )
            );

        // -------------------------------------------------
        // Stop if another summary label starts.
        // -------------------------------------------------

        if (
            preg_match(
                '/^(subtotal|sub total|taxable amount|service tax|additional charges?|discount|round off|cgst|sgst|igst|total amount|grand total|paid amount|paid|balance|terms)/i',
                $normalizedNext
            )
        ) {

            break;
        }

        $amount =
            $extractLastAmount(
                $nextLine
            );

        if (
            $amount !== null
        ) {

            return $amount;
        }
    }

    return null;
};


// =====================================================
// LOOP THROUGH OCR SUMMARY
// =====================================================

foreach (
    $lines as $index => $line
) {

    $normalized =
        strtolower(
            preg_replace(
                '/\s+/',
                ' ',
                str_replace(
                    "\t",
                    ' ',
                    trim($line)
                )
            )
        );

    // =================================================
    // ADDITIONAL CHARGES
    // =================================================

    if (
        preg_match(
            '/additional\s+charges?|service\s+tax|service\s+charge/i',
            $normalized
        )
    ) {

        $amount =
            $extractAmountAfterLabel(
                $lines,
                $index,
                'additional'
            );

        if (
            $amount !== null
        ) {

            $additionalChargeTotal +=
                abs($amount);
        }

        continue;
    }


    // =================================================
    // DISCOUNT
    // =================================================

    if (
        preg_match(
            '/\bdiscount\b/i',
            $normalized
        )
    ) {

        $amount =
            $extractAmountAfterLabel(
                $lines,
                $index,
                'discount'
            );

        if (
            $amount !== null
        ) {

            $discountAmount =
                abs($amount);
        }

        continue;
    }


    // =================================================
    // ROUND OFF
    // =================================================

    if (
        preg_match(
            '/round\s*off/i',
            $normalized
        )
    ) {

        $amount =
            $extractAmountAfterLabel(
                $lines,
                $index,
                'round'
            );

        if (
            $amount !== null
        ) {

            // Preserve negative sign
            $directAmount =
                $extractLastAmount(
                    $line
                );

            if (
                $directAmount !== null
            ) {

                $roundOff =
                    $directAmount;

            } else {

                $roundOff =
                    $amount;
            }
        }

        continue;
    }


    // =================================================
    // TOTAL AMOUNT
    // =================================================

    if (
        preg_match(
            '/\btotal\s+amount\b|\bgrand\s+total\b/i',
            $normalized
        )
    ) {

        $amount =
            $extractAmountAfterLabel(
                $lines,
                $index,
                'total'
            );

        if (
            $amount !== null
        ) {

            $invoiceGrandTotal =
                abs($amount);
        }

        continue;
    }


    // =================================================
    // PAID AMOUNT
    // =================================================

    if (
        preg_match(
            '/\bpaid\s+amount\b|\bamount\s+paid\b|\bpaid\b|\breceived\b/i',
            $normalized
        )
    ) {

        $amount =
            $extractAmountAfterLabel(
                $lines,
                $index,
                'paid'
            );

        if (
            $amount !== null
        ) {

            $receivedAmount =
                abs($amount);
        }

        continue;
    }


    // =================================================
    // BALANCE
    // =================================================

    if (
        preg_match(
            '/^\s*balance\b|\bbalance\s+amount\b/i',
            $normalized
        )
    ) {

        $amount =
            $extractAmountAfterLabel(
                $lines,
                $index,
                'balance'
            );

        if (
            $amount !== null
        ) {

            $balanceAmount =
                abs($amount);
        }

        continue;
    }
}

    // =====================================================
// VALIDATE OCR DISCOUNT AGAINST GRAND TOTAL
// =====================================================

if (
    $invoiceGrandTotal !== null
) {

    $expectedBeforeDiscount =
        $subtotal
        + $totalTax
        + $additionalChargeTotal
        + $roundOff;

    $possibleDiscount =
        round(
            $expectedBeforeDiscount -
            $invoiceGrandTotal,
            2
        );

    /*
     * If OCR discount is obviously inconsistent
     * with the invoice grand total, use the amount
     * mathematically supported by the invoice.
     */

    if (
        $possibleDiscount >= 0 &&
        $possibleDiscount <=
            ($expectedBeforeDiscount * 0.90)
    ) {

        $ocrDifference =
            abs(
                $discountAmount -
                $possibleDiscount
            );

        /*
         * Allow small OCR differences,
         * but reject clearly corrupted values.
         */

        if (
            $ocrDifference > 1.00
        ) {

            $discountAmount =
                $possibleDiscount;
        }
    }
}

    // =====================================================
    // 16. CALCULATE GRAND TOTAL
    // =====================================================

    $calculatedGrandTotal =
        $subtotal
        + $totalTax
        + $additionalChargeTotal
        - $discountAmount
        + $roundOff;

    $calculatedGrandTotal =
        round(
            $calculatedGrandTotal,
            2
        );

    // =====================================================
    // 17. USE OCR GRAND TOTAL IF AVAILABLE
    // =====================================================

    if (
        $invoiceGrandTotal !== null
    ) {

        $grandTotal =
            round(
                $invoiceGrandTotal,
                2
            );

    } else {

        $grandTotal =
            $calculatedGrandTotal;
    }

   // =====================================================
// PAYMENT
// =====================================================

if (
    $receivedAmount === null
) {

    $receivedAmount = 0;
}

$receivedAmount =
    round(
        $receivedAmount,
        2
    );

// =====================================================
// PAYMENT CONSISTENCY
// =====================================================

if (
    $receivedAmount > $grandTotal
) {

    /*
     * OCR may have captured a wrong number.
     *
     * Never allow paid amount to exceed
     * invoice grand total.
     */

    $receivedAmount =
        $grandTotal;
}

// =====================================================
// BALANCE
// =====================================================

$calculatedBalance =
    round(
        $grandTotal -
        $receivedAmount,
        2
    );

// If OCR balance exists, use it only when sane.
if (
    $balanceAmount !== null &&
    $balanceAmount >= 0 &&
    $balanceAmount <= $grandTotal
) {

    /*
     * Prefer mathematically consistent balance.
     */

    if (
        abs(
            $balanceAmount -
            $calculatedBalance
        ) <= 1.00
    ) {

        $balanceAmount =
            $calculatedBalance;

    } else {

        $balanceAmount =
            $calculatedBalance;
    }

} else {

    $balanceAmount =
        $calculatedBalance;
}

if (
    abs($balanceAmount) < 0.01
) {

    $balanceAmount = 0;
}

    // =====================================================
    // 20. RESPONSE
    // =====================================================

    return response()->json([

        'success' =>
            true,

       'place_of_supply' =>
    $placeOfSupply,

        'party' => [

            'id' =>
                1,

            'party_name' =>
                $partyName ??
                'Unknown',
        ],

        'items' =>
            $items,

        'subtotal' =>
            round(
                $subtotal,
                2
            ),

        'total_tax' =>
            round(
                $totalTax,
                2
            ),

        'additional_charge_total' =>
            round(
                $additionalChargeTotal,
                2
            ),

        'discount_amount' =>
            round(
                $discountAmount,
                2
            ),

        'round_off' =>
            round(
                $roundOff,
                2
            ),

        'grand_total' =>
            round(
                $grandTotal,
                2
            ),

        'received_amount' =>
            round(
                $receivedAmount,
                2
            ),

        'balance_amount' =>
            round(
                $balanceAmount,
                2
            ),

        'raw_text' =>
            $rawText,
    ]);
}

}
