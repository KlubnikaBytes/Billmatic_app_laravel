<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Invoice; // ✅ ADD THIS LINE
use App\Models\ReceivedPayment;
use App\Models\Purchase;



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
        'contact_person_name' => 'nullable|string|max:255',
        'dob'                 => 'nullable|date',
    ]);

    $data['user_id'] = $user->id;

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
            'party_name'     => $p->party->party_name ?? 'Unknown Party',
            'number'         => $p->purchase_number,
            'date'           => $p->purchase_date,
            'due_date'       => $p->due_date,
            'status'         => $p->status,
            'grand_total'    => (float) $p->grand_total,
            'balance_amount' => (float) $p->balance_amount,
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
}
