<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Party;
//need to add//
use App\Models\PartyCategory;
use App\Models\Invoice; // ✅ ADD THIS LINE
use App\Models\ReceivedPayment;
use App\Models\Purchase;
use App\Models\Expense;
//need//
use App\Models\PurchaseOrder;
use App\Models\DebitNote;
use App\Models\SalesReturn;
use App\Models\CreditNote;
use App\Models\Quotation;
use App\Models\DeliveryChallan;
use App\Models\ProformaInvoice;
use App\Models\AutomatedBillRun;



class PartyController extends Controller
{


    public function index(Request $request)
{
    $user = $request->user();
    $search = $request->query('search');

    $query = Party::where('user_id', $user->id);

    // 🔍 SEARCH LOGIC
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('party_name', 'LIKE', "%$search%")
              ->orWhere('contact_number', 'LIKE', "%$search%")
              ->orWhere('gst_number', 'LIKE', "%$search%")
              ->orWhere('pan_number', 'LIKE', "%$search%")
              ->orWhere('contact_person_name', 'LIKE', "%$search%")
              ->orWhere('dob', 'LIKE', "%$search%");
        });
    }

    $parties = $query->orderBy('party_name')->get();

    return response()->json([
        'success' => true,
        'data' => $parties,
    ]);
}

    // public function store(Request $request)
    // {
    //     $user = $request->user();

    //     $data = $request->validate([
    //         'party_name'          => 'required|string|max:255',
    //         'contact_number'      => 'nullable|string|max:20',
    //         'party_type'          => 'required|in:customer,supplier',

    //         'gst_number'          => 'nullable|string|max:50',
    //         'pan_number'          => 'nullable|string|max:50',

    //         'billing_street'      => 'nullable|string|max:255',
    //         'billing_state'       => 'nullable|string|max:100',
    //         'billing_pincode'     => 'nullable|string|max:10',
    //         'billing_city'        => 'nullable|string|max:100',

    //         'opening_balance'     => 'nullable|numeric',
    //         'opening_balance_type' => 'required|in:receive,pay', // ✅ ADD

    //         'credit_period_days'  => 'nullable|integer',
    //         'credit_limit'        => 'nullable|numeric',

    //         'party_category_id'   => 'nullable|exists:party_categories,id',
    //         'contact_person_name' => 'nullable|string|max:255',
    //         'dob'                 => 'nullable|date',
    //     ]);

    //     $data['user_id'] = $user->id;

    //     $party = Party::create($data);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Party created successfully',
    //         'data'    => $party,
    //     ], 201);
    // }

    public function store(Request $request)
{
    $user = $request->user();

    // ✅ FORCE UPPERCASE (IMPORTANT)
    $request->merge([
        'gst_number' => $request->gst_number ? strtoupper($request->gst_number) : null,
        'pan_number' => $request->pan_number ? strtoupper($request->pan_number) : null,
    ]);

    $data = $request->validate([
        'party_name'     => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:20',
        'party_type'     => 'required|in:customer,supplier',

        // ✅ FIXED VALIDATION
        'gst_number' => [
            'nullable',
            'regex:/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/'
        ],

        'pan_number' => [
            'nullable',
            'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'
        ],

        'billing_street'  => 'nullable|string|max:255',
        'billing_state'   => 'nullable|string|max:100',
        'billing_pincode' => 'nullable|string|max:10',
        'billing_city'    => 'nullable|string|max:100',

        'opening_balance'      => 'nullable|numeric',
        'opening_balance_type' => 'required|in:receive,pay',

        'credit_period_days' => 'nullable|integer',
        'credit_limit'       => 'nullable|numeric',

        'party_category_id'   => 'nullable|exists:party_categories,id',
        //need to add//
        'party_category_name' => 'nullable|string|max:255',
        'contact_person_name' => 'nullable|string|max:255',
        'dob'                 => 'nullable|date',
    ]);

    $data['user_id'] = $user->id;

    //need to add//
    // ========================================
// PARTY CATEGORY
// ========================================
if (!empty($data['party_category_name'])) {

    $category = PartyCategory::firstOrCreate(
        [
            'user_id' => $user->id,
            'name'    => trim($data['party_category_name']),
        ]
    );

    $data['party_category_id'] = $category->id;
}

// Do not send this temporary field to Party model
unset($data['party_category_name']);

    $party = Party::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Party created successfully',
        'data'    => $party,
    ], 201);
}


//     public function update(Request $request, $id)
// {
//     $party = Party::where('user_id', auth()->id())
//         ->findOrFail($id);

//     $data = $request->validate([
//         'party_name'           => 'required|string|max:255',
//         'contact_number'       => 'nullable|string|max:20',
//         'party_type'           => 'required|in:customer,supplier',

//         'gst_number'           => 'nullable|string|max:50',
//         'pan_number'           => 'nullable|string|max:50',

//         'billing_street'       => 'nullable|string|max:255',
//         'billing_state'        => 'nullable|string|max:100',
//         'billing_pincode'      => 'nullable|string|max:10',
//         'billing_city'         => 'nullable|string|max:100',

//         'opening_balance'      => 'nullable|numeric',
//         'opening_balance_type' => 'required|in:receive,pay',

//         'credit_period_days'   => 'nullable|integer',
//         'credit_limit'         => 'nullable|numeric',

//         'party_category_id'    => 'nullable|exists:party_categories,id',
//         'contact_person_name'  => 'nullable|string|max:255',
//         'dob'                  => 'nullable|date',
//     ]);

//     $party->update($data);

//     return response()->json([
//         'success' => true,
//         'message' => 'Party updated successfully',
//         'data'    => $party,
//     ]);
// }

public function update(Request $request, $id)
{
    $party = Party::where('user_id', auth()->id())->findOrFail($id);

    // ✅ FORCE UPPERCASE
    $request->merge([
        'gst_number' => $request->gst_number ? strtoupper($request->gst_number) : null,
        'pan_number' => $request->pan_number ? strtoupper($request->pan_number) : null,
    ]);

    $data = $request->validate([
        'party_name'     => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:20',
        'party_type'     => 'required|in:customer,supplier',

        // ✅ FIXED VALIDATION
        'gst_number' => [
            'nullable',
            'regex:/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/'
        ],

        'pan_number' => [
            'nullable',
            'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'
        ],

        'billing_street'  => 'nullable|string|max:255',
        'billing_state'   => 'nullable|string|max:100',
        'billing_pincode' => 'nullable|string|max:10',
        'billing_city'    => 'nullable|string|max:100',

        'opening_balance'      => 'nullable|numeric',
        'opening_balance_type' => 'required|in:receive,pay',

        'credit_period_days' => 'nullable|integer',
        'credit_limit'       => 'nullable|numeric',

        'party_category_id'   => 'nullable|exists:party_categories,id',
        'contact_person_name' => 'nullable|string|max:255',
        'dob'                 => 'nullable|date',
    ]);

    $party->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Party updated successfully',
        'data'    => $party,
    ]);
}


public function destroy($id)
{
    $party = Party::where('user_id', auth()->id())->findOrFail($id);

    $party->delete();

    return response()->json([
        'success' => true,
        'message' => 'Party deleted successfully',
    ]);
}



     public function invoices($id)
{
    // 🔐 Ensure party belongs to logged-in user
    $party = Party::where('user_id', auth()->id())
        ->findOrFail($id);

    // 📄 Fetch only pending invoices
    $invoices = Invoice::where('party_id', $id)
        ->where('balance_amount', '>', 0)
        ->orderBy('invoice_date')
        ->get([
            'id',
            'invoice_number',
            'invoice_date',
            'grand_total',
            'received_amount',
            'balance_amount',
        ]);

    return response()->json([
        'success'  => true,
        'party'    => [
            'id'                    => $party->id,
            'party_name'            => $party->party_name,
            'opening_balance'       => $party->opening_balance,
            'opening_balance_type'  => $party->opening_balance_type,
        ],
        'invoices' => $invoices,
    ]);
}


// ========================================
// 🔄 ALL TRANSACTIONS (HOME DASHBOARD)
// ========================================
public function allTransactions(Request $request)
{
    $user = $request->user();

    // 📅 Date filters from query params
    $from = $request->query('from');
    $to   = $request->query('to');

    // ✅ Get all party IDs of logged-in user
    $partyIds = Party::where('user_id', $user->id)->pluck('id');

    // =====================================
    // 📄 INVOICES QUERY
    // =====================================
    $invoiceQuery = Invoice::whereIn('party_id', $partyIds)
        ->with('party:id,party_name');

    if ($from && $to) {
        $invoiceQuery->whereBetween('invoice_date', [$from, $to]);
    }

    $invoices = $invoiceQuery
        ->get()
        ->map(function ($inv) {
            return [
                'type'           => 'invoice',

                //need it add in server
                  // ✅ ADD THIS LINE (VERY IMPORTANT)
                'invoice_id'     => $inv->id,

                'party_name'     => $inv->party->party_name ?? 'Unknown Party',
                'number'         => $inv->invoice_number,
                'date'           => $inv->invoice_date,
                'due_date'       => $inv->due_date,
                'status'         => $inv->status,
                'grand_total'    => (float) $inv->grand_total,
                'balance_amount' => (float) $inv->balance_amount,
            ];
        });

    // =====================================
    // 💰 PAYMENTS QUERY
    // =====================================
    $paymentQuery = ReceivedPayment::whereIn('party_id', $partyIds)
        ->with('party:id,party_name');

    if ($from && $to) {
        $paymentQuery->whereBetween('payment_date', [$from, $to]);
    }

    $payments = $paymentQuery
        ->get()
        ->map(function ($pay) {
            return [
                'type'       => 'payment',
                // ✅ VERY IMPORTANT
                'payment_id' => $pay->id,
                'party_name' => $pay->party->party_name ?? 'Unknown Party',
                // 'number'     => $pay->payment_number,
                // 'number'     => $pay->id,
                'number'     => $pay->payment_number,
                'date'       => $pay->payment_date,
                'amount'     => (float) $pay->amount,
            ];
        });

        // =====================================
// 🛒 PURCHASE QUERY (ADD THIS)
// =====================================
$purchaseQuery = Purchase::whereIn('party_id', $partyIds)
    ->with('party:id,party_name');

if ($from && $to) {
    $purchaseQuery->whereBetween('purchase_date', [$from, $to]);
}

$purchases = $purchaseQuery
    ->get()
    ->map(function ($p) {
        return [
            'type'           => 'purchase', // ✅ IMPORTANT
            // ✅ IMPORTANT
            'purchase_id' => $p->id,
            'party_name'     => $p->party->party_name ?? 'Unknown Party',
            'number'         => $p->purchase_number,
            'date'           => $p->purchase_date,
            'due_date'       => $p->due_date,
            'status'         => $p->status,
            'grand_total'    => (float) $p->grand_total,
            'balance_amount' => (float) $p->balance_amount,
        ];
    });

    //need//

    // =====================================
// 💸 EXPENSE QUERY
// =====================================
$expenseQuery = Expense::with('category')
    ->where('user_id', $user->id);

if ($from && $to) {
    $expenseQuery->whereBetween('expense_date', [$from, $to]);
}

$expenses = $expenseQuery
    ->get()
    ->map(function ($expense) {
        return [
            'type'       => 'expense',
            'expense_id' => $expense->id,
            'party_name' => $expense->party_name
                ?: ($expense->category->name ?? 'Expense'),
            'number'     => $expense->expense_number,
            'date'       => $expense->expense_date,
            'amount'     => (float) $expense->total_amount,
            'category_name' => $expense->category->name ?? '',
            'status' => $expense->status,
        ];
    });

    // =====================================
// PURCHASE ORDER QUERY
// =====================================
$purchaseOrderQuery = PurchaseOrder::where('user_id', $user->id)
    ->with('party:id,party_name');

if ($from && $to) {
    $purchaseOrderQuery->whereBetween('order_date', [$from, $to]);
}

$purchaseOrders = $purchaseOrderQuery
    ->get()
    ->map(function ($order) {
        return [
            'type' => 'purchase_order',
            'purchase_order_id' => $order->id,
            'party_name' => $order->party->party_name ?? 'Unknown Party',
            'number' => $order->order_number,
            'date' => $order->order_date,
            'expiry_date' => $order->expiry_date,
            'grand_total' => (float) $order->grand_total,
            'status' => $order->converted_purchase_id
                ? 'converted'
                : 'ordered',
        ];
    });

    // =====================================
// DEBIT NOTE TRANSACTIONS
// =====================================
$debitNoteQuery = DebitNote::where('user_id', $user->id)
    ->with('party:id,party_name');

if ($from && $to) {
    $debitNoteQuery->whereBetween('debit_note_date', [
        substr($from, 0, 10),
        substr($to, 0, 10),
    ]);
}

$debitNotes = $debitNoteQuery
    ->get()
    ->map(function ($note) {
        $total = (float) $note->grand_total;
        $balance = (float) $note->balance_amount;

        $status = $balance <= 0.01
            ? 'credited'
            : ($balance < $total
                ? 'partially_credited'
                : 'not_credited');

        return [
            'type' => 'debit_note',
            'debit_note_id' => $note->id,
            'party_name' => $note->party->party_name ?? 'Unknown Party',
            'number' => $note->debit_note_number,
            'date' => $note->debit_note_date,
            'grand_total' => $total,
            'received_amount' => (float) $note->received_amount,
            'balance_amount' => $balance,
            'status' => $status,
        ];
    });

    // =====================================
// SALES RETURN TRANSACTIONS
// =====================================
$salesReturnQuery = SalesReturn::where('user_id', $user->id)
    ->with('party:id,party_name');

if ($from && $to) {
    $salesReturnQuery->whereBetween('sales_return_date', [
        substr($from, 0, 10),
        substr($to, 0, 10),
    ]);
}

$salesReturns = $salesReturnQuery
    ->get()
    ->map(function ($return) {
        $total = (float) $return->grand_total;
        $paid = (float) $return->paid_amount;
        $balance = (float) $return->balance_amount;

        $status = $balance <= 0.01
            ? 'refunded'
            : ($paid > 0.01
                ? 'partially_refunded'
                : 'not_refunded');

        return [
            'type' => 'sales_return',
            'sales_return_id' => $return->id,
            'party_name' => $return->party->party_name ?? 'Unknown Party',
            'number' => $return->sales_return_number,
            'date' => $return->sales_return_date,
            'grand_total' => $total,
            'paid_amount' => $paid,
            'balance_amount' => $balance,
            'status' => $status,
        ];
    });

    // =====================================
// CREDIT NOTE TRANSACTIONS
// =====================================
$creditNoteQuery = CreditNote::where('user_id', $user->id)
    ->with('party:id,party_name');

if ($from && $to) {
    $creditNoteQuery->whereBetween('credit_note_date', [
        substr($from, 0, 10),
        substr($to, 0, 10),
    ]);
}

$creditNotes = $creditNoteQuery
    ->get()
    ->map(function ($note) {
        $total = (float) $note->grand_total;
        $paid = (float) $note->paid_amount;
        $balance = (float) $note->balance_amount;

        $status = $balance <= 0.01
            ? 'refunded'
            : ($paid > 0.01
                ? 'partially_refunded'
                : 'not_refunded');

        return [
            'type' => 'credit_note',
            'credit_note_id' => $note->id,
            'party_name' => $note->party->party_name ?? 'Unknown Party',
            'number' => $note->credit_note_number,
            'date' => $note->credit_note_date,
            'grand_total' => $total,
            'paid_amount' => $paid,
            'balance_amount' => $balance,
            'status' => $status,
        ];
    });


    // =====================================
    // QUOTATION / ESTIMATE TRANSACTIONS
    // =====================================
    $quotationQuery = Quotation::where('user_id', $user->id)
        ->with('party:id,party_name');

    if ($from && $to) {
        $quotationQuery->whereBetween('quotation_date', [
            substr($from, 0, 10),
            substr($to, 0, 10),
        ]);
    }

    $quotations = $quotationQuery
        ->get()
        ->map(function ($quotation) {
            return [
                'type' => 'quotation',
                'quotation_id' => $quotation->id,
                'party_name' => $quotation->party->party_name
                    ?? 'Unknown Party',
                'number' => $quotation->quotation_number,
                'date' => $quotation->quotation_date,
                'expiry_date' => $quotation->expiry_date,
                'grand_total' => (float) $quotation->grand_total,
                'status' => $quotation->status,
            ];
        });


    // =====================================
    // DELIVERY CHALLAN TRANSACTIONS
    // =====================================
    $deliveryChallanQuery = DeliveryChallan::where(
        'user_id',
        $user->id
    )->with('party:id,party_name');

    if ($from && $to) {
        $deliveryChallanQuery->whereBetween('challan_date', [
            substr($from, 0, 10),
            substr($to, 0, 10),
        ]);
    }

    $deliveryChallans = $deliveryChallanQuery
        ->get()
        ->map(function ($challan) {
            return [
                'type' => 'delivery_challan',
                'delivery_challan_id' => $challan->id,
                'party_name' => $challan->party->party_name
                    ?? 'Unknown Party',
                'number' => $challan->challan_number,
                'date' => $challan->challan_date
                    ->format('Y-m-d'),
                'grand_total' => (float) $challan->grand_total,
                'status' => $challan->status,
                'converted_invoice_id' =>
                    $challan->converted_invoice_id,
            ];
        });


    // =====================================
    // PROFORMA INVOICE TRANSACTIONS
    // =====================================
    $proformaInvoiceQuery = ProformaInvoice::where(
        'user_id',
        $user->id
    )->with('party:id,party_name');

    if ($from && $to) {
        $proformaInvoiceQuery->whereBetween(
            'proforma_date',
            [
                substr($from, 0, 10),
                substr($to, 0, 10),
            ]
        );
    }

    $proformaInvoices = $proformaInvoiceQuery
        ->get()
        ->map(function ($proforma) {
            return [
                'type' => 'proforma_invoice',
                'proforma_invoice_id' => $proforma->id,
                'party_name' => $proforma->party->party_name
                    ?? 'Unknown Party',
                'number' => $proforma->proforma_number,
                'date' => $proforma->proforma_date
                    ->format('Y-m-d'),
                'expiry_date' => $proforma->expiry_date
                    ? $proforma->expiry_date->format('Y-m-d')
                    : null,
                'grand_total' => (float) $proforma->grand_total,
                'status' => $proforma->status,
                'converted_invoice_id' =>
                    $proforma->converted_invoice_id,
            ];
        });




    // =====================================
    // 🔀 MERGE + SORT (LATEST FIRST)
    // =====================================
    // $transactions = $invoices
    //     ->merge($payments)
    //     ->sortByDesc('date')
    //     ->values();

    // return response()->json([
    //     'success'      => true,
    //     'transactions' => $transactions,
    // ]);

    // =====================================
// 🔀 MERGE + SORT
// =====================================
$transactions = $invoices
    ->merge($payments)
    ->merge($purchases) // ✅ ADD THIS
    //need//
    ->merge($purchaseOrders)
    ->merge($expenses)
    ->merge($debitNotes)
    ->merge($salesReturns)
    ->merge($creditNotes)
    ->merge($quotations)
    ->merge($deliveryChallans)
    ->merge($proformaInvoices)
    ->sortByDesc('date')
    ->values();

// =====================================
// 📄 PAGINATION (ADD THIS)
// =====================================
$page = (int) $request->query('page', 1);
$perPage = 10;

$total = $transactions->count();

$items = $transactions
    ->slice(($page - 1) * $perPage, $perPage)
    ->values();

$lastPage = (int) ceil($total / $perPage);

// =====================================
// ✅ FINAL RESPONSE
// =====================================
return response()->json([
    'success' => true,
    'data' => $items,
    'current_page' => $page,
    'last_page' => $lastPage,
]);
}



// ========================================
// 🔄 TRANSACTIONS (INVOICES + PAYMENTS)
// ========================================
public function transactions($id)
{
    // 🔐 Ensure party belongs to logged-in user
    $party = Party::where('user_id', auth()->id())
        ->findOrFail($id);

    // 📄 Invoices
    $invoices = Invoice::where('party_id', $id)
        ->get()
        ->map(function ($inv) {
            return [
                'type'            => 'invoice',
                'id'              => $inv->id,
                'number'          => $inv->invoice_number,
                'date'            => $inv->invoice_date,

                // ✅ ADD THIS LINE
                'due_date'        => $inv->due_date,
                'grand_total'     => $inv->grand_total,
                'received_amount' => $inv->received_amount,
                'balance_amount'  => $inv->balance_amount,
                'status'          => $inv->status,
            ];
        });

    // 💰 Received Payments
    $payments = ReceivedPayment::where('party_id', $id)
        ->get()
        ->map(function ($pay) {
            return [
                'type'         => 'payment',
                'id'           => $pay->id,
                // 'number'       => $pay->payment_number,
                // 'number'     => $pay->id,
                'number'       => $pay->payment_number,
                'date'         => $pay->payment_date,
                'amount'       => $pay->amount,
                'payment_mode' => $pay->payment_mode,
            ];
        });

    // 🔀 Merge + sort by date (DESC)
    $transactions = $invoices
        ->merge($payments)
        ->sortByDesc('date')
        ->values();


    return response()->json([
    'success' => true,
    'party'   => [
        'id'                    => $party->id,
        'party_name'            => $party->party_name,
        'party_type'            => $party->party_type,
        'contact_number'        => $party->contact_number,
        'gst_number'            => $party->gst_number,
        'pan_number'            => $party->pan_number,
        'billing_address'       => trim("{$party->billing_street}, {$party->billing_city}, {$party->billing_state} {$party->billing_pincode}", ', '),
        'shipping_address'      => $party->party_name,
        'opening_balance'       => (float) $party->opening_balance,
        'opening_balance_type'  => $party->opening_balance_type,
        'credit_period_days'    => $party->credit_period_days,
        'credit_limit'          => $party->credit_limit,
    ],
    'transactions' => $transactions,
]);

}

// ================================
// ✏️ GET SINGLE PARTY (FOR EDIT)
// ================================
public function show($id)
{
    $party = Party::where('user_id', auth()->id())
        ->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => $party,
    ]);
}



public function dashboardTotals(Request $request)
{
    $user = $request->user();

    $toCollect = Party::where('user_id', $user->id)
        ->where('opening_balance_type', 'receive')
        ->sum('opening_balance');

    $toPay = Party::where('user_id', $user->id)
        ->where('opening_balance_type', 'pay')
        ->sum('opening_balance');

    $weekStart = \Carbon\Carbon::now()->startOfWeek();
    $weekEnd   = \Carbon\Carbon::now()->endOfWeek();

    $thisWeekSales = Invoice::where('user_id', $user->id)
        ->whereBetween('invoice_date', [$weekStart, $weekEnd])
        ->sum('grand_total');

    return response()->json([
        'success' => true,
        'data' => [
            'to_collect'      => (float) $toCollect,
            'to_pay'          => (float) $toPay,
            'this_week_sales' => (float) $thisWeekSales,
        ],
    ]);
}

//need to add//


// GET /api/party-categories
// ========================================
// PARTY CATEGORIES
// ========================================

public function categories(Request $request)
{
    $userId = $request->user()->id;

    $categories = PartyCategory::where('user_id', $userId)
        ->withCount('parties')
        ->orderBy('name')
        ->get(['id', 'name']);

    return response()->json([
        'success' => true,
        'data' => $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'party_count' => $category->parties_count,
            ];
        }),
    ]);
}
// POST /api/party-categories
public function storeCategory(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $name = trim($data['name']);

    $category = PartyCategory::firstOrCreate([
        'user_id' => $request->user()->id,
        'name' => $name,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Category saved successfully',
        'data' => $category,
    ], $category->wasRecentlyCreated ? 201 : 200);
}


/**
 * List parties belonging to the managed user's business owner.
 */
public function managedIndex(Request $request)
{
    $managedUser = $request->user('managed-api');

    abort_unless($managedUser, 401, 'Unauthenticated.');

    $query = Party::where('user_id', $managedUser->owner_user_id);
    $search = $request->query('search');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('party_name', 'LIKE', "%{$search}%")
              ->orWhere('contact_number', 'LIKE', "%{$search}%")
              ->orWhere('gst_number', 'LIKE', "%{$search}%")
              ->orWhere('pan_number', 'LIKE', "%{$search}%")
              ->orWhere('contact_person_name', 'LIKE', "%{$search}%")
              ->orWhere('dob', 'LIKE', "%{$search}%");
        });
    }

    return response()->json([
        'success' => true,
        'data' => $query->orderBy('party_name')->get(),
    ]);
}

/**
 * Create a party under the business owner's account.
 */
public function managedStore(Request $request)
{
    $managedUser = $request->user('managed-api');

    abort_unless($managedUser, 401, 'Unauthenticated.');

    // Initially allow only Partner and Salesman to create parties.
    abort_unless(
        in_array($managedUser->role, ['Partner', 'Salesman'], true),
        403,
        'Your role cannot create parties.'
    );

    $ownerId = $managedUser->owner_user_id;

    $request->merge([
        'gst_number' => $request->gst_number
            ? strtoupper($request->gst_number) : null,
        'pan_number' => $request->pan_number
            ? strtoupper($request->pan_number) : null,
    ]);

    $data = $request->validate([
        'party_name' => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:20',
        'party_type' => 'required|in:customer,supplier',
        'gst_number' => [
            'nullable',
            'regex:/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/',
        ],
        'pan_number' => [
            'nullable',
            'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
        ],
        'billing_street' => 'nullable|string|max:255',
        'billing_state' => 'nullable|string|max:100',
        'billing_pincode' => 'nullable|string|max:10',
        'billing_city' => 'nullable|string|max:100',
        'opening_balance' => 'nullable|numeric',
        'opening_balance_type' => 'required|in:receive,pay',
        'credit_period_days' => 'nullable|integer',
        'credit_limit' => 'nullable|numeric',
        'party_category_id' => [
            'nullable',
            \Illuminate\Validation\Rule::exists('party_categories', 'id')
                ->where('user_id', $ownerId),
        ],
        'party_category_name' => 'nullable|string|max:255',
        'contact_person_name' => 'nullable|string|max:255',
        'dob' => 'nullable|date',
    ]);

    if (!empty($data['party_category_name'])) {
        $category = PartyCategory::firstOrCreate([
            'user_id' => $ownerId,
            'name' => trim($data['party_category_name']),
        ]);

        $data['party_category_id'] = $category->id;
    }

    unset($data['party_category_name']);

    // Critical: parties belong to the owner, not the managed user's ID.
    $data['user_id'] = $ownerId;

    $party = Party::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Party created successfully.',
        'data' => $party,
    ], 201);
}

/**
 * Show one party only if it belongs to the owner's account.
 */
public function managedShow(Request $request, $id)
{
    $managedUser = $request->user('managed-api');

    abort_unless($managedUser, 401, 'Unauthenticated.');

    $party = Party::where('user_id', $managedUser->owner_user_id)
        ->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => $party,
    ]);
}

/**
 * Update a party belonging to the owner's account.
 */
public function managedUpdate(Request $request, $id)
{
    $managedUser = $request->user('managed-api');

    abort_unless($managedUser, 401, 'Unauthenticated.');

    abort_unless(
        in_array($managedUser->role, ['Partner', 'Salesman'], true),
        403,
        'Your role cannot update parties.'
    );

    $ownerId = $managedUser->owner_user_id;

    $party = Party::where('user_id', $ownerId)->findOrFail($id);

    $request->merge([
        'gst_number' => $request->gst_number
            ? strtoupper($request->gst_number) : null,
        'pan_number' => $request->pan_number
            ? strtoupper($request->pan_number) : null,
    ]);

    $data = $request->validate([
        'party_name' => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:20',
        'party_type' => 'required|in:customer,supplier',
        'gst_number' => [
            'nullable',
            'regex:/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/',
        ],
        'pan_number' => [
            'nullable',
            'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
        ],
        'billing_street' => 'nullable|string|max:255',
        'billing_state' => 'nullable|string|max:100',
        'billing_pincode' => 'nullable|string|max:10',
        'billing_city' => 'nullable|string|max:100',
        'opening_balance' => 'nullable|numeric',
        'opening_balance_type' => 'required|in:receive,pay',
        'credit_period_days' => 'nullable|integer',
        'credit_limit' => 'nullable|numeric',
        'party_category_id' => [
            'nullable',
            \Illuminate\Validation\Rule::exists('party_categories', 'id')
                ->where('user_id', $ownerId),
        ],
        'contact_person_name' => 'nullable|string|max:255',
        'dob' => 'nullable|date',
    ]);

    $party->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Party updated successfully.',
        'data' => $party,
    ]);
}

/**
 * List categories belonging to the business owner.
 */
public function managedCategories(Request $request)
{
    $managedUser = $request->user('managed-api');

    abort_unless($managedUser, 401, 'Unauthenticated.');

    $categories = PartyCategory::where(
        'user_id',
        $managedUser->owner_user_id
    )
        ->withCount('parties')
        ->orderBy('name')
        ->get(['id', 'name']);

    return response()->json([
        'success' => true,
        'data' => $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'party_count' => $category->parties_count,
            ];
        }),
    ]);
}

/**
 * Create a category for the business owner's account.
 */
public function managedStoreCategory(Request $request)
{
    $managedUser = $request->user('managed-api');

    abort_unless($managedUser, 401, 'Unauthenticated.');

    abort_unless(
        in_array($managedUser->role, ['Partner', 'Salesman'], true),
        403,
        'Your role cannot create party categories.'
    );

    $data = $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $category = PartyCategory::firstOrCreate([
        'user_id' => $managedUser->owner_user_id,
        'name' => trim($data['name']),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Category saved successfully.',
        'data' => $category,
    ], $category->wasRecentlyCreated ? 201 : 200);
}

//need//
public function partyStatement(Request $request, $id)
{
    $user = $request->user();

    $party = Party::where('user_id', $user->id)
        ->findOrFail($id);

    $from = $request->query('from');
    $to = $request->query('to');

    $invoiceQuery = Invoice::where('user_id', $user->id)
        ->where('party_id', $party->id);

    if ($from) {
        $invoiceQuery->whereDate('invoice_date', '>=', $from);
    }

    if ($to) {
        $invoiceQuery->whereDate('invoice_date', '<=', $to);
    }

    $invoices = $invoiceQuery->get()->map(function ($invoice) {
        return [
            'date' => $invoice->invoice_date,
            'reference' => $invoice->invoice_number,
            'type' => 'Invoice',
            'debit' => (float) $invoice->grand_total,
            'credit' => 0.0,
            'sort_id' => $invoice->id,
        ];
    });

    $paymentQuery = ReceivedPayment::where(
        'user_id',
        $user->id
    )->where('party_id', $party->id);

    if ($from) {
        $paymentQuery->whereDate('payment_date', '>=', $from);
    }

    if ($to) {
        $paymentQuery->whereDate('payment_date', '<=', $to);
    }

    $payments = $paymentQuery->get()->map(function ($payment) {
        return [
            'date' => $payment->payment_date,
            'reference' => $payment->payment_number,
            'type' => 'Payment',
            'debit' => 0.0,
            'credit' => (float) $payment->amount,
            'sort_id' => $payment->id,
        ];
    });

    $transactions = $invoices
        ->merge($payments)
        ->sortBy('date')
        ->values();

    $openingBalance = (float) ($party->opening_balance ?? 0);
    $balance = $openingBalance;

    $transactions = $transactions->map(function ($row) use (&$balance) {
        $balance += $row['debit'] - $row['credit'];

        unset($row['sort_id']);

        $row['balance'] = round($balance, 2);

        return $row;
    });

    return response()->json([
        'success' => true,
        'party' => [
            'id' => $party->id,
            'party_name' => $party->party_name,
            'party_type' => $party->party_type,
            'contact_number' => $party->contact_number,
            'opening_balance' => $openingBalance,
            'opening_balance_type' => $party->opening_balance_type,
        ],
        'transactions' => $transactions,
        'closing_balance' => round($balance, 2),
    ]);
}


/**
 * Party-wise outstanding report.
 * Works with owner and managed-user authentication.
 */
public function partyWiseOutstanding(Request $request)
{
    $managedUser = $request->user('managed-api');

    if ($managedUser) {
        $ownerId = $managedUser->owner_user_id;
    } else {
        $owner = $request->user('api');

        abort_unless($owner, 401, 'Unauthenticated.');

        $ownerId = $owner->id;
    }

    $parties = Party::where('user_id', $ownerId)
        ->where('opening_balance', '>', 0)
        ->orderBy('party_name')
        ->get([
            'id',
            'party_name',
            'party_type',
            'contact_number',
            'opening_balance',
            'opening_balance_type',
        ]);

    $data = $parties->map(function ($party) {
        $balance = (float) $party->opening_balance;
        $type = strtolower($party->opening_balance_type ?? '');

        return [
            'id' => $party->id,
            'party_name' => $party->party_name,
            'party_type' => $party->party_type,
            'contact_number' => $party->contact_number,
            'receivable' => $type === 'receive' ? $balance : 0.0,
            'payable' => $type === 'pay' ? $balance : 0.0,
        ];
    })->values();

    return response()->json([
        'success' => true,
        'data' => $data,
        'summary' => [
            'total_receivable' => round(
                $data->sum('receivable'),
                2
            ),
            'total_payable' => round(
                $data->sum('payable'),
                2
            ),
        ],
    ]);
}


}
