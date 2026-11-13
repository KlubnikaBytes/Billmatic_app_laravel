<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Party;
use App\Models\ExpenseItemCatalog;
use App\Models\ExpenseAdditionalCharge;

class ExpenseController extends Controller
{
    // GET: List expense categories
    public function categories(Request $request)
    {
        $categories = ExpenseCategory::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    // POST: Create expense category
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expense_categories', 'name')
                    ->where('user_id', $request->user()->id),
            ],
        ]);

        $category = ExpenseCategory::create([
            'user_id' => $request->user()->id,
            'name' => trim($validated['name']),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense category created successfully.',
            'data' => $category,
        ], 201);
    }

    // GET: Generate next expense number
    public function nextNumber(Request $request)
    {
        $lastExpense = Expense::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->first();

        $next = 1;

        if ($lastExpense) {
            $lastNumber = (int) preg_replace(
                '/\D/',
                '',
                $lastExpense->expense_number
            );

            $next = $lastNumber + 1;
        }

        return response()->json([
            'success' => true,
            'expense_number' => 'EXP-' . str_pad($next, 4, '0', STR_PAD_LEFT),
        ]);
    }

    // GET: List expenses
    public function index(Request $request)
    {
        $expenses = Expense::with([
    'category',
    'items',
    'party',
    'additionalCharges',
])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $expenses,
        ]);
    }

    // POST: Save expense with items
    public function store(Request $request)
    {
        $validated = $request->validate([
            'original_invoice_number' => 'nullable|string|max:255',
            'expense_category_id' => [
                'nullable',
                'integer',
                Rule::exists('expense_categories', 'id')
                    ->where('user_id', $request->user()->id),
            ],
            'expense_date' => 'required|date',
            'is_gst' => 'required|boolean',
            'party_id' => [
               'nullable',
               'required_if:is_gst,1',
               'integer',
               Rule::exists('parties', 'id')
                    ->where('user_id', $request->user()->id),
            ],

            'discount_percentage' => 'nullable|numeric|min:0|max:100',

            'additional_charges' => 'nullable|array',

            'additional_charges.*.charge_name' => 'required|string|max:255',
            'additional_charges.*.amount' => 'required|numeric|min:0',
            'additional_charges.*.tax_percent' => 'required|numeric|min:0|max:100',
            'items.*.price_type' => 'required|in:With Tax,Without Tax',

            'party_name' => 'nullable|string|max:255',
            'payment_mode' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $userId = $request->user()->id;

        $party = null;

if (!empty($validated['party_id'])) {
    $party = Party::where('user_id', $userId)
        ->where('id', $validated['party_id'])
        ->firstOrFail();
}

        try {
            $expense = DB::transaction(function () use ($validated, $userId, $party) {

                // Generate expense number
                $lastExpense = Expense::where('user_id', $userId)
                    ->lockForUpdate()
                    ->orderByDesc('id')
                    ->first();

                $next = 1;

                if ($lastExpense) {
                    $lastNumber = (int) preg_replace(
                        '/\D/',
                        '',
                        $lastExpense->expense_number
                    );

                    $next = $lastNumber + 1;
                }

                $expenseNumber = 'EXP-' . str_pad(
                    $next,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

              $subtotal = 0;
              $totalGst = 0;
              $itemsToSave = [];

foreach ($validated['items'] as $item) {
    $quantity = (float) $item['quantity'];
    $rate = (float) $item['rate'];

    $gstPercent = !empty($validated['is_gst'])
        ? (float) ($item['gst_percent'] ?? 0)
        : 0;

    $priceType = $item['price_type'];

    $enteredAmount = round($quantity * $rate, 2);

    if ($priceType === 'With Tax' && $gstPercent > 0) {
        $lineAmount = $enteredAmount;
        $baseAmount = round(
            $lineAmount / (1 + ($gstPercent / 100)),
            2
        );
        $gstAmount = round($lineAmount - $baseAmount, 2);
    } else {
        $baseAmount = $enteredAmount;
        $gstAmount = round(
            $baseAmount * $gstPercent / 100,
            2
        );
        $lineAmount = round($baseAmount + $gstAmount, 2);
    }

    $subtotal += $baseAmount;
    $totalGst += $gstAmount;

    $itemsToSave[] = [
        'item_name' => $item['item_name'],
        'quantity' => $quantity,
        'rate' => $rate,
        'price_type' => $priceType,
        'gst_percent' => $gstPercent,
        'gst_amount' => $gstAmount,
        'amount' => $lineAmount,
    ];
}

$subtotal = round($subtotal, 2);
$totalGst = round($totalGst, 2);


$additionalChargesData = $validated['additional_charges'] ?? [];

$chargeSubtotal = 0;
$chargeGst = 0;
$chargesToSave = [];

foreach ($additionalChargesData as $charge) {
    $amount = round((float) $charge['amount'], 2);
    $taxPercent = !empty($validated['is_gst'])
    ? (float) $charge['tax_percent']
    : 0;

    $taxAmount = round(
        $amount * $taxPercent / 100,
        2
    );

    $chargeSubtotal += $amount;
    $chargeGst += $taxAmount;

    $chargesToSave[] = [
        'charge_name' => trim($charge['charge_name']),
        'amount' => $amount,
        'tax_percent' => $taxPercent,
        'tax_amount' => $taxAmount,
    ];
}

$chargeSubtotal = round($chargeSubtotal, 2);
$chargeGst = round($chargeGst, 2);

// Include additional charge GST in total GST.
$totalGst = round($totalGst + $chargeGst, 2);

// Discount is calculated on the amount before discount.
$discountPercentage = (float) (
    $validated['discount_percentage'] ?? 0
);

$amountBeforeDiscount = round(
    $subtotal + $totalGst + $chargeSubtotal,
    2
);

$discount = round(
    $amountBeforeDiscount * $discountPercentage / 100,
    2
);

$totalAmount = round(
    $amountBeforeDiscount - $discount,
    2
);


if ($discount > $amountBeforeDiscount) {
    throw \Illuminate\Validation\ValidationException::withMessages([
        'discount_percentage' => [
            'Discount cannot exceed the expense amount.'
        ],
    ]);
}

$expense = Expense::create([
    'user_id' => $userId,
    'party_id' => $party?->id,
    'party_name' => $party?->party_name,
    'expense_number' => $expenseNumber,
    'original_invoice_number' =>
        $validated['original_invoice_number'] ?? null,
    'expense_category_id' =>
        $validated['expense_category_id'] ?? null,
    'expense_date' => $validated['expense_date'],
    'is_gst' => $validated['is_gst'],
    'subtotal' => $subtotal,
    'gst_amount' => $totalGst,
    'additional_charges' => $chargeSubtotal,
'discount' => $discount,
'discount_percentage' => $discountPercentage,
    'total_amount' => $totalAmount,
    'status' => 'paid',
    'payment_mode' => $validated['payment_mode'],
    'notes' => $validated['notes'] ?? null,
]);

$expense->items()->createMany($itemsToSave);
if (!empty($chargesToSave)) {
    $expense->additionalCharges()->createMany($chargesToSave);
}

                return $expense;
            });

            return response()->json([
                'success' => true,
                'message' => 'Expense saved successfully.',
                'data' => $expense->load([
    'category',
    'items',
    'party',
    'additionalCharges',
]),
            ], 201);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to save expense.',
            ], 500);
        }
    }

    // GET: View one expense
    public function show(Request $request, $id)
    {
        $expense = Expense::with([
    'category',
    'items',
    'party',
    'additionalCharges',
])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $expense,
        ]);
    }

    // PUT: Update Expense Category
public function updateCategory(Request $request, $id)
{
    $category = ExpenseCategory::where('user_id', $request->user()->id)
        ->findOrFail($id);

    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('expense_categories', 'name')
                ->where('user_id', $request->user()->id)
                ->ignore($category->id),
        ],
    ]);

    $category->update([
        'name' => trim($validated['name']),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Expense category updated successfully.',
        'data' => $category,
    ]);
}

// DELETE: Deactivate Expense Category
public function destroyCategory(Request $request, $id)
{
    $category = ExpenseCategory::where('user_id', $request->user()->id)
        ->findOrFail($id);

    $category->update([
        'is_active' => false,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Expense category deleted successfully.',
    ]);
}

// GET: List previously saved expense items for the logged-in user
// public function items(Request $request)
// {
//     $userId = $request->user()->id;

//     $latestItems = DB::table('expense_items as ei')
//         ->join('expenses as e', 'e.id', '=', 'ei.expense_id')
//         ->where('e.user_id', $userId)
//         ->selectRaw('MAX(ei.id) as latest_id')
//         ->groupBy('ei.item_name');

//     $items = DB::table('expense_items as ei')
//         ->joinSub($latestItems, 'latest', function ($join) {
//             $join->on('ei.id', '=', 'latest.latest_id');
//         })
//         ->select(
//             'ei.item_name',
//             'ei.rate',
//             'ei.gst_percent'
//         )
//         ->orderBy('ei.item_name')
//         ->get();

//     return response()->json([
//         'success' => true,
//         'data' => $items,
//     ]);
// }

// GET: List saved expense item catalogue
public function items(Request $request)
{
    $items = ExpenseItemCatalog::where('user_id', $request->user()->id)
        ->orderBy('item_name')
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'rate' => $item->purchase_price,
                'gst_percent' => $item->gst_percent,
                'item_type' => $item->item_type,
                'unit' => $item->unit,
                'hsn_code' => $item->hsn_code,
                'itc_applicable' => $item->itc_applicable,
                'price_type' => $item->price_type,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $items,
    ]);
}


// POST: Save a reusable expense item
public function storeItem(Request $request)
{
    $validated = $request->validate([
        'item_name' => 'required|string|max:255',
        'item_type' => 'required|in:Product,Service',
        'purchase_price' => 'required|numeric|min:0',
        'price_type' => 'required|in:With Tax,Without Tax',
        'unit' => 'required|string|max:50',
        'gst_percent' => 'required|numeric|min:0|max:100',
        'hsn_code' => 'nullable|string|max:50',
        'itc_applicable' => 'required|string|max:100',
    ]);

    $item = ExpenseItemCatalog::create([
        'user_id' => $request->user()->id,
        'item_name' => trim($validated['item_name']),
        'item_type' => $validated['item_type'],
        'purchase_price' => $validated['purchase_price'],
        'price_type' => $validated['price_type'],
        'unit' => $validated['unit'],
        'gst_percent' => $validated['gst_percent'],
        'hsn_code' => $validated['hsn_code'] ?? null,
        'itc_applicable' => $validated['itc_applicable'],
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Expense item saved successfully.',
        'data' => $item,
    ], 201);
}

// GET: Expense Category Report
public function categoryReport(Request $request)
{
    $validated = $request->validate([
        'from' => 'required|date',
        'to' => 'required|date|after_or_equal:from',
    ]);

    $userId = $request->user()->id;

    $expenses = DB::table('expenses as e')
        ->leftJoin('expense_categories as ec', function ($join) {
            $join->on('e.expense_category_id', '=', 'ec.id')
                ->on('e.user_id', '=', 'ec.user_id');
        })
        ->where('e.user_id', $userId)
        ->whereDate('e.expense_date', '>=', $validated['from'])
        ->whereDate('e.expense_date', '<=', $validated['to'])
        ->select(
            DB::raw("COALESCE(ec.name, 'Uncategorized') as name"),
            DB::raw('SUM(e.total_amount) as amount')
        )
        ->groupBy('e.expense_category_id', 'ec.name')
        ->orderBy('name')
        ->get();

    $totalExpense = DB::table('expenses')
        ->where('user_id', $userId)
        ->whereDate('expense_date', '>=', $validated['from'])
        ->whereDate('expense_date', '<=', $validated['to'])
        ->sum('total_amount');

    return response()->json([
        'success' => true,
        'data' => [
            'categories' => $expenses,
            'total_expense' => (float) $totalExpense,
            'from' => $validated['from'],
            'to' => $validated['to'],
        ],
    ]);
}

}
