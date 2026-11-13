<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InvoiceItem;
use App\Models\Invoice;

//need//
use Illuminate\Support\Facades\DB;
use App\Models\Party;
use App\Models\PurchaseItem;
use App\Models\Purchase;


// need to add in server
use Barryvdh\DomPDF\Facade\Pdf;


class ItemController extends Controller
{


    public function show(Request $request, $id)
{
    $user = $request->user();

    $item = Item::where('id', $id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    $currentStock = $item->opening_stock;
    $purchasePrice = $item->purchase_price ?? 0;

    $item->stock_value = round($currentStock * $purchasePrice, 2);

return response()->json([
    'success' => true,
    'data' => $item,
]);
}



    public function index(Request $request)
{
    $user = $request->user();
    $search = $request->query('search');

    $query = Item::where('user_id', $user->id);

    // 🔍 APPLY SEARCH
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%$search%")
              ->orWhere('item_code', 'LIKE', "%$search%")
              ->orWhere('hsn_code', 'LIKE', "%$search%")
              ->orWhere('barcode', 'LIKE', "%$search%")

              // JSON fields (important 🔥)
              ->orWhereJsonContains('imei_list', $search)
              ->orWhere('custom_fields', 'LIKE', "%$search%");
        });
    }

    $items = $query->orderBy('name')->get();

    return response()->json([
        'success' => true,
        'data' => $items,
    ]);
}

    public function store(Request $request)
    {
        $user = $request->user();

        //need//

       // Service: Purchase Price and Opening Stock are not required
if ($request->input('item_type') === 'service') {
    $request->merge([
        'purchase_price' => 0,
        'opening_stock' => 0,
        'inventory_tracking_by' => 'Qty',
    ]);
} elseif ($request->input('purchase_price') === '' ||
          $request->input('purchase_price') === null) {
    $request->merge([
        'purchase_price' => 0,
    ]);
}

        $data = $request->validate([

            // 'name'                 => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:items,name,NULL,id,user_id,' . $request->user()->id,

            'item_type'            => 'required|in:product,service',
            'inventory_tracking_by'=> 'nullable|string|max:50',
            'unit'                 => 'nullable|string|max:50',
            'sales_price'          => 'nullable|numeric',
            'purchase_price'       => 'nullable|numeric',
            'gst_percent'          => 'nullable|numeric',
            'hsn_code'             => 'nullable|string|max:50',

            'opening_stock'        => 'nullable|numeric',
            'stock_as_of_date'     => 'nullable|date',
            'item_code'            => 'nullable|string|max:100',
            'barcode'              => 'nullable|string|max:255',

                // ✅ CHANGED
            'item_categories'       => 'nullable|array',
            'item_categories.*'     => 'string|max:100',
            'description'          => 'nullable|string',
            'custom_fields'        => 'nullable|array',
            'imei_list'            => 'nullable|array',   // <-- NEW
            'low_stock_alert'    => 'nullable|boolean',
            'low_stock_quantity' => 'nullable|numeric',

            'show_in_online_store'  => 'nullable|boolean', // ✅ NEW
           'image'                 => 'nullable|image|max:2048', // ✅ NEW

        ]);

        // Database requires numeric values instead of NULL.
        $data['purchase_price'] = $data['purchase_price'] ?? 0;
        $data['opening_stock'] = $data['opening_stock'] ?? 0;

        $data['user_id'] = $user->id;

        //need to add
        // GST is 0 when no GST is selected
        $data['gst_percent'] = $data['gst_percent'] ?? 0;

        // 📸 Image upload
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('items', 'public');
        $data['image_path'] = $path;
    }

    // ✅ Checkbox safe handling
    $data['show_in_online_store'] = $request->boolean('show_in_online_store');

    //need//


    // ✅ CALCULATE STOCK VALUE
   $data['stock_value'] = $this->calculateStockValue(
    $data['opening_stock'] ?? 0,
    $data['purchase_price'] ?? 0
);



        $item = Item::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
            'data'    => $item,
        ], 201);
    }

public function timeline($id, Request $request)
{
    $user = $request->user();

    $item = Item::where('id', $id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    // 🔹 Opening stock
    $timeline = collect([
        [
            'type'    => 'opening',
            'title'   => 'Opening Stock',
            'change'  => (float) $item->opening_stock,
            'date'    => $item->stock_as_of_date ?? $item->created_at,
        ]
    ]);

    // 🔹 Invoice movements
    $invoiceItems = InvoiceItem::where('item_id', $item->id)
        ->with('invoice')
        ->get()
        ->map(function ($row) {
            return [
                'type'   => 'invoice',
                'title'  => 'Invoice',
                'change' => -1 * (float) $row->qty,
                'date'   => $row->invoice->invoice_date,
            ];
        });

    // 🔹 Merge & sort (OLD → NEW)
    $timeline = $timeline
        ->merge($invoiceItems)
        ->sortBy('date')   // 🔥 ASC for correct balance
        ->values();

    // 🔢 Calculate running balance
    $balance = 0;
    foreach ($timeline as &$row) {
        $balance += $row['change'];
        $row['balance'] = $balance;
    }

    // 🔁 Show latest first in UI
    $timeline = $timeline->reverse()->values();

    return response()->json([
        'success' => true,
        'data'    => $timeline,
    ]);
}

public function update(Request $request, $id)
{
    $user = $request->user();

    $item = Item::where('id', $id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    if ($request->input('item_type') === 'service') {
        $request->merge([
            'purchase_price' => 0,
            'opening_stock' => 0,
            'inventory_tracking_by' => 'Qty',
        ]);
    } elseif ($request->input('purchase_price') === '') {
        $request->merge([
            'purchase_price' => 0,
        ]);
    }

    $data = $request->validate([
        'name' => 'required|string|max:255|unique:items,name,' . $item->id . ',id,user_id,' . $user->id,
        'item_type' => 'required|in:product,service',
        'inventory_tracking_by'=> 'nullable|string|max:50',
        'unit' => 'nullable|string|max:50',
        'sales_price' => 'nullable|numeric',
        'purchase_price' => 'nullable|numeric',
        'gst_percent' => 'nullable|numeric',
        'hsn_code' => 'nullable|string|max:50',
        'opening_stock' => 'nullable|numeric',
        'stock_as_of_date' => 'nullable|date',
        'item_code' => 'nullable|string|max:100',
        'barcode' => 'nullable|string|max:255',
        'item_categories' => 'nullable|array',
        'item_categories.*' => 'string|max:100',
        'description' => 'nullable|string',
        'imei_list' => 'nullable|array',
        'low_stock_alert' => 'nullable|boolean',
        'low_stock_quantity' => 'nullable|numeric',
        'show_in_online_store' => 'nullable|boolean',
        'image' => 'nullable|image|max:2048',
    ]);

    //need//

     // Opening Stock optional for both Product and Service
        // If not entered, default to 0
        $data['opening_stock'] = $data['opening_stock'] ?? 0;

    if ($request->hasFile('image')) {
        $data['image_path'] = $request->file('image')->store('items', 'public');
    }

    $data['show_in_online_store'] = $request->boolean('show_in_online_store');

    //need to add
    // GST is 0 when no GST is selected
    $data['gst_percent'] = $data['gst_percent'] ?? 0;

    // ✅ RECALCULATE STOCK VALUE
    $data['stock_value'] = $this->calculateStockValue(
    $data['opening_stock'] ?? $item->opening_stock,
    $data['purchase_price'] ?? $item->purchase_price
);


    $item->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Item updated successfully',
        'data' => $item,
    ]);
}

public function destroy(Request $request, $id)
{
    $user = $request->user();

    $item = Item::where('id', $id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    $item->delete();

    return response()->json([
        'success' => true,
        'message' => 'Item deleted successfully'
    ]);
}

// public function stockSummary(Request $request)
// {
//     $user = $request->user();

//     $items = Item::where('user_id', $user->id)->get();

//     return response()->json([
//         'success' => true,
//         'data' => [
//             'total_stock_value' => round($items->sum('stock_value'), 2),
//             'items' => $items->map(function ($item) {
//                 return [
//                     'id'       => $item->id,
//                     'name'     => $item->name,
//                     'barcode'  => $item->barcode,
//                     'quantity' => (float) $item->opening_stock,
//                     'unit'     => $item->unit,
//                     'value'    => (float) $item->stock_value,
//                 ];
//             }),
//         ],
//     ]);
// }


// need to add in server
// public function stockSummary(Request $request)
// {
//     $user = $request->user();

//     $items = Item::where('user_id', $user->id)->get();

//     return response()->json([
//         'success' => true,
//         'data' => [
//             'total_stock_value' => round($items->sum('stock_value'), 2),
//          'items' => $items->map(function ($item) {
//     return [
//         'id' => $item->id,
//         'name' => $item->name,
//         'barcode' => $item->barcode,

//         // ✅ NEW FIELDS (for report screen)
//         'batch_no' => '-', // keep default or update later
//         'item_code' => $item->item_code,
//         'purchase_price' => (float) ($item->purchase_price ?? 0),
//         'selling_price' => (float) ($item->sales_price ?? 0),

//         // ✅ EXISTING
//         'quantity' => (float) $item->opening_stock,
//         'unit' => $item->unit,
//         'value' => (float) $item->stock_value,
//     ];
// }),
//         ],
//     ]);
// }

public function stockSummary(Request $request)
{
    $user = $request->user();

    $items = Item::where('user_id', $user->id)->get();

    return response()->json([
        'success' => true,
        'data' => [
            'total_stock_value' => round($items->map(function ($item) {

                // ✅ USE DIRECT STOCK (NO EXTRA CALCULATION)
                return ($item->opening_stock ?? 0) * ($item->purchase_price ?? 0);

            })->sum(), 2),

            'items' => $items->map(function ($item) {

                $currentStock = $item->opening_stock ?? 0;
                $stockValue = $currentStock * ($item->purchase_price ?? 0);

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'barcode' => $item->barcode,

                    'batch_no' => '-',
                    'item_code' => $item->item_code,
                    'purchase_price' => (float) ($item->purchase_price ?? 0),
                    'selling_price' => (float) ($item->sales_price ?? 0),

                    // ✅ FINAL FIX
                    'quantity' => (float) $currentStock,
                    'unit' => $item->unit,
                    'value' => round($stockValue, 2),
                ];
            }),
        ],
    ]);
}

//need to add in server
public function stockSummaryPdf(Request $request)
{
    $user = $request->user();

    $items = Item::where('user_id', $user->id)->get();

    $total = $items->sum('stock_value');

    // ✅ ADD THIS (TOTAL QTY)
    $totalQty = $items->sum('opening_stock');

    $pdf = Pdf::loadView('pdf.stock_summary', [
        'items' => $items,
        'total' => $total,
        'totalQty' => $totalQty, // ✅ PASS TO BLADE
        'date'  => now()->format('d/m/Y'),
    ]);

    return $pdf->download('stock-summary.pdf'); // ✅ force download
}

//need//
// ===============================================
// PARTY REPORT BY ITEM
// ===============================================

public function partyReportByItem(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        'item_id' => 'required|integer',
        'from' => 'required|date',
        'to' => 'required|date|after_or_equal:from',
    ]);

    $item = Item::where('user_id', $user->id)
        ->findOrFail($data['item_id']);

    $sales = DB::table('invoice_items as ii')
        ->join('invoices as inv', 'inv.id', '=', 'ii.invoice_id')
        ->leftJoin('parties as p', 'p.id', '=', 'inv.party_id')
        ->where('inv.user_id', $user->id)
        ->where('ii.item_id', $item->id)
        ->whereBetween('inv.invoice_date', [$data['from'], $data['to']])
        ->selectRaw("
            inv.party_id,
            COALESCE(p.party_name, 'Cash Sale') as party_name,
            SUM(ii.qty) as sales_qty,
            SUM(ii.line_total) as sales_amount
        ")
        ->groupBy('inv.party_id', 'p.party_name')
        ->get();

    $purchases = DB::table('purchase_items as pi')
        ->join('purchases as pur', 'pur.id', '=', 'pi.purchase_id')
        ->leftJoin('parties as p', 'p.id', '=', 'pur.party_id')
        ->where('pur.user_id', $user->id)
        ->where('pi.item_id', $item->id)
        ->whereBetween('pur.purchase_date', [$data['from'], $data['to']])
        ->selectRaw("
            pur.party_id,
            COALESCE(p.party_name, 'Cash Purchase') as party_name,
            SUM(pi.qty) as purchase_qty,
            SUM(pi.line_total) as purchase_amount
        ")
        ->groupBy('pur.party_id', 'p.party_name')
        ->get();

    $rows = [];

    foreach ($sales as $sale) {
        $key = $sale->party_id === null
            ? 'cash_sale'
            : 'party_' . $sale->party_id;

        $rows[$key] = [
            'party_name' => $sale->party_name,
            'sales_qty' => (float) $sale->sales_qty,
            'sales_amount' => (float) $sale->sales_amount,
            'purchase_qty' => 0,
            'purchase_amount' => 0,
        ];
    }

    foreach ($purchases as $purchase) {
        $key = $purchase->party_id === null
            ? 'cash_purchase'
            : 'party_' . $purchase->party_id;

        if (!isset($rows[$key])) {
            $rows[$key] = [
                'party_name' => $purchase->party_name,
                'sales_qty' => 0,
                'sales_amount' => 0,
                'purchase_qty' => 0,
                'purchase_amount' => 0,
            ];
        }

        $rows[$key]['purchase_qty'] =
            (float) $purchase->purchase_qty;

        $rows[$key]['purchase_amount'] =
            (float) $purchase->purchase_amount;
    }

    $business = \App\Models\BusinessDetail::where(
        'user_id',
        $user->id
    )->first();

    return response()->json([
        'success' => true,
        'item' => [
            'id' => $item->id,
            'name' => $item->name,
        ],
        'business' => [
            'name' => $business->business_name ?? '',
            'phone' => $business->business_phone_number
                ?? $user->mobile ?? '',
        ],
        'data' => array_values($rows),
    ]);
}



public function itemReportByParty(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'from' => 'required|date',
        'to' => 'required|date|after_or_equal:from',
        'party_id' => 'nullable|integer',
    ]);

    $from = \Carbon\Carbon::parse($validated['from'])->startOfDay();
    $to = \Carbon\Carbon::parse($validated['to'])->endOfDay();

    if (!empty($validated['party_id'])) {
        Party::where('user_id', $user->id)
            ->findOrFail($validated['party_id']);
    }

    $sales = DB::table('invoice_items as ii')
        ->join('invoices as inv', 'inv.id', '=', 'ii.invoice_id')
        ->join('items as i', 'i.id', '=', 'ii.item_id')
        ->join('parties as p', 'p.id', '=', 'inv.party_id')
        ->where('inv.user_id', $user->id)
        ->where('i.user_id', $user->id)
        ->whereBetween('inv.invoice_date', [$from, $to])
        ->when(
            !empty($validated['party_id']),
            fn ($q) => $q->where('inv.party_id', $validated['party_id'])
        )
        ->selectRaw("
            inv.party_id,
            ii.item_id,
            SUM(ii.qty) as sales_qty,
            SUM(ii.line_total) as sales_amount
        ")
        ->groupBy('inv.party_id', 'ii.item_id');

    $purchases = DB::table('purchase_items as pi')
        ->join('purchases as pur', 'pur.id', '=', 'pi.purchase_id')
        ->join('items as i', 'i.id', '=', 'pi.item_id')
        ->where('pur.user_id', $user->id)
        ->where('i.user_id', $user->id)
        ->whereBetween('pur.purchase_date', [$from, $to])
        ->when(
            !empty($validated['party_id']),
            fn ($q) => $q->where('pur.party_id', $validated['party_id'])
        )
        ->selectRaw("
            pur.party_id,
            pi.item_id,
            SUM(pi.qty) as purchase_qty,
            SUM(pi.line_total) as purchase_amount
        ")
        ->groupBy('pur.party_id', 'pi.item_id');

    $salesRows = $sales->get()->keyBy(
        fn ($row) => $row->party_id . '-' . $row->item_id
    );

    $purchaseRows = $purchases->get()->keyBy(
        fn ($row) => $row->party_id . '-' . $row->item_id
    );

    $keys = $salesRows->keys()
        ->merge($purchaseRows->keys())
        ->unique();

    $data = $keys->map(function ($key) use ($salesRows, $purchaseRows, $user) {
        $sale = $salesRows->get($key);
        $purchase = $purchaseRows->get($key);

        $source = $sale ?? $purchase;

        $item = Item::where('id', $source->item_id)
            ->where('user_id', $user->id)
            ->first();

        $party = Party::where('id', $source->party_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$item || !$party) {
            return null;
        }

        return [
            'party_name' => $party->party_name,
            'item_code' => $item->item_code ?? '',
            'item_name' => $item->name,
            'sales_qty' => (float) ($sale->sales_qty ?? 0),
            'sales_amount' => (float) ($sale->sales_amount ?? 0),
            'purchase_qty' => (float) ($purchase->purchase_qty ?? 0),
            'purchase_amount' => (float) ($purchase->purchase_amount ?? 0),
        ];
    })->filter()->values();

    $business = \App\Models\BusinessDetail::where(
        'user_id',
        $user->id
    )->first();

    return response()->json([
        'success' => true,
        'business' => [
            'name' => $business->business_name ?? '',
            'phone' => $business->business_phone_number
                ?? $user->mobile
                ?? '',
        ],
        'parties' => Party::where('user_id', $user->id)
            ->orderBy('party_name')
            ->get(['id', 'party_name']),
        'data' => $data,
    ]);
}


public function itemsSaleSummary(Request $request)
{
    $validated = $request->validate([
        'from' => 'required|date',
        'to' => 'required|date|after_or_equal:from',
    ]);

    $user = $request->user();

    $rows = DB::table('invoice_items as ii')
        ->join('invoices as inv', 'inv.id', '=', 'ii.invoice_id')
        ->join('items as i', 'i.id', '=', 'ii.item_id')
        ->where('inv.user_id', $user->id)
        ->where('i.user_id', $user->id)
        ->whereBetween('inv.invoice_date', [
            $validated['from'],
            $validated['to'],
        ])
        ->select(
            'i.id as item_id',
            'i.name as item_name',
            'i.unit as unit'
        )
        ->selectRaw('SUM(ii.qty) as quantity')
        ->groupBy('i.id', 'i.name', 'i.unit')
        ->orderBy('i.name')
        ->get()
        ->map(function ($row) {
            return [
                'item_id' => $row->item_id,
                'item_name' => $row->item_name,
                'unit' => $row->unit ?? '',
                'quantity' => (float) $row->quantity,
            ];
        });

    return response()->json([
        'success' => true,
        'data' => [
            'items' => $rows,
            'total_item_quantity' => $rows->sum('quantity'),
        ],
    ]);
}





public function itemTimeline(Request $request, $id)
{
    $user = $request->user();

    $item = Item::where('user_id', $user->id)
        ->findOrFail($id);

    $validated = $request->validate([
        'from' => 'nullable|date',
        'to' => 'nullable|date|after_or_equal:from',
    ]);

    $from = !empty($validated['from'])
        ? \Carbon\Carbon::parse($validated['from'])->startOfDay()
        : null;

    $to = !empty($validated['to'])
        ? \Carbon\Carbon::parse($validated['to'])->endOfDay()
        : null;

    $timeline = collect();

    // Opening stock is the starting balance, not a movement
    // to add again when viewing a later date range.
    $openingDate = \Carbon\Carbon::parse(
        $item->stock_as_of_date ?? $item->created_at
    );

     // opening_stock holds the current stored quantity; remove manual
     // adjustments from the baseline so they are not counted twice.
     $manualAdjustmentTotal = DB::table('item_stock_adjustments')
         ->where('item_id', $item->id)
         ->where('user_id', $user->id)
         ->selectRaw("COALESCE(SUM(CASE WHEN type = 'add' THEN quantity ELSE -quantity END), 0) AS net")
         ->value('net');
     $openingStock = (float) ($item->opening_stock ?? 0) - (float) $manualAdjustmentTotal;

    $timeline->push([
        'type' => 'opening',
        'title' => 'Opening Stock',
        'change' => $openingStock,
        'date' => $openingDate->toDateTimeString(),
        'unit' => $item->unit ?? '',
        'notes' => '',
        '_sort' => $openingDate->timestamp,
    ]);

    // Sales invoices reduce stock.
    $sales = DB::table('invoice_items as ii')
        ->join('invoices as inv', 'inv.id', '=', 'ii.invoice_id')
        ->where('ii.item_id', $item->id)
        ->where('inv.user_id', $user->id)
        ->select(
            'ii.qty',
            'inv.invoice_date as movement_date',
            'inv.invoice_number as reference'
        )
        ->get();

    foreach ($sales as $sale) {
        if (!$sale->movement_date) {
            continue;
        }

        $date = \Carbon\Carbon::parse($sale->movement_date);

        $timeline->push([
            'type' => 'invoice',
            'title' => 'Sales Invoice',
            'change' => -1 * (float) $sale->qty,
            'date' => $date->toDateTimeString(),
            'unit' => $item->unit ?? '',
            'notes' => $sale->reference ?? '',
            '_sort' => $date->timestamp,
        ]);
    }

    // Purchases increase stock.
    $purchases = DB::table('purchase_items as pi')
        ->join('purchases as pur', 'pur.id', '=', 'pi.purchase_id')
        ->where('pi.item_id', $item->id)
        ->where('pur.user_id', $user->id)
        ->select(
            'pi.qty',
            'pur.purchase_date as movement_date',
            'pur.purchase_number as reference'
        )
        ->get();

    foreach ($purchases as $purchase) {
        if (!$purchase->movement_date) {
            continue;
        }

        $date = \Carbon\Carbon::parse($purchase->movement_date);

        $timeline->push([
            'type' => 'purchase',
            'title' => 'Purchase Bill',
            'change' => (float) $purchase->qty,
            'date' => $date->toDateTimeString(),
            'unit' => $item->unit ?? '',
            'notes' => $purchase->reference ?? '',
            '_sort' => $date->timestamp,
        ]);
    }

     // Manual stock adjustments.
     $adjustments = DB::table('item_stock_adjustments')
         ->where('item_id', $item->id)
         ->where('user_id', $user->id)
         ->orderBy('id')
         ->get();

     foreach ($adjustments as $adjustment) {
         $date = \Carbon\Carbon::parse($adjustment->adjustment_date);
         $timeline->push([
             'type' => 'adjustment',
             'title' => $adjustment->type === 'add' ? 'Add Stock' : 'Reduce Stock',
             'change' => ($adjustment->type === 'add' ? 1 : -1) * (float) $adjustment->quantity,
             'date' => $date->toDateTimeString(),
             'unit' => $item->unit ?? '',
             'notes' => $adjustment->notes ?? '',
             '_sort' => $date->timestamp,
         ]);
     }

    // Calculate stock as of the start of the selected period.
    $allRows = $timeline
        ->sortBy('_sort')
        ->values();

    $balance = 0.0;

    foreach ($allRows as &$row) {
        $balance += (float) $row['change'];
        $row['balance'] = round($balance, 2);
    }
    unset($row);

    // Keep only transactions in the requested period.
    $filtered = $allRows->filter(function ($row) use ($from, $to) {
        $date = \Carbon\Carbon::parse($row['date']);

        if ($from && $date->lt($from)) {
            return false;
        }

        if ($to && $date->gt($to)) {
            return false;
        }

        return true;
    });

    return response()->json([
        'success' => true,
        'item' => [
            'id' => $item->id,
            'name' => $item->name,
            'unit' => $item->unit ?? '',
        ],
        'data' => $filtered
            ->sortByDesc('_sort')
            ->map(function ($row) {
                unset($row['_sort']);
                return $row;
            })
            ->values(),
    ]);
}




 // Product stock adjustment: updates current stock and records an audit trail.
 public function adjustStock(Request $request, $id)
 {
     $user = $request->user();
     $data = $request->validate([
         'type' => 'required|in:add,reduce',
         'quantity' => 'required|numeric|gt:0',
         'date' => 'required|date',
         'notes' => 'nullable|string|max:2000',
     ]);

     return DB::transaction(function () use ($user, $id, $data) {
         $item = Item::where('id', $id)
             ->where('user_id', $user->id)
             ->lockForUpdate()
             ->firstOrFail();

         if ($item->item_type !== 'product') {
             return response()->json([
                 'success' => false,
                 'message' => 'Stock adjustments are only available for products.',
             ], 422);
         }

         $current = (float) ($item->opening_stock ?? 0);
         $qty = (float) $data['quantity'];
         $newStock = $current + ($data['type'] === 'add' ? $qty : -$qty);

         if ($newStock < 0) {
             return response()->json([
                 'success' => false,
                 'message' => 'Cannot reduce more than available stock.',
             ], 422);
         }

         DB::table('item_stock_adjustments')->insert([
             'user_id' => $user->id,
             'item_id' => $item->id,
             'type' => $data['type'],
             'quantity' => $qty,
             'stock_before' => $current,
             'stock_after' => $newStock,
             'adjustment_date' => $data['date'],
             'notes' => $data['notes'] ?? null,
             'created_at' => now(),
             'updated_at' => now(),
         ]);

         $item->opening_stock = $newStock;
         $item->stock_value = $this->calculateStockValue(
             $newStock,
             $item->purchase_price ?? 0
         );
         $item->save();

         return response()->json([
             'success' => true,
             'message' => 'Stock adjusted successfully',
             'data' => $item->fresh(),
         ]);
     });
 }

// ✅ ADD THIS METHOD (REQUIRED)
private function calculateStockValue($qty, $purchasePrice)
{
    $qty = (float) $qty;
    $purchasePrice = (float) $purchasePrice;

    return round($qty * $purchasePrice, 2);
}


public function partyPrices(Request $request, $id)
{
    $user = $request->user();

    Item::where('user_id', $user->id)->findOrFail($id);

    $prices = DB::table('item_party_prices as ipp')
        ->join('parties as p', 'p.id', '=', 'ipp.party_id')
        ->where('ipp.user_id', $user->id)
        ->where('ipp.item_id', $id)
        ->select(
            'ipp.id',
            'ipp.party_id',
            'p.party_name',
            'ipp.sales_price'
        )
        ->orderBy('p.party_name')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $prices,
    ]);
}

public function savePartyPrice(Request $request, $id)
{
    $user = $request->user();

    Item::where('user_id', $user->id)->findOrFail($id);

    $data = $request->validate([
        'party_id' => 'required|integer',
        'sales_price' => 'required|numeric|min:0',
    ]);

    Party::where('user_id', $user->id)
        ->findOrFail($data['party_id']);

    DB::table('item_party_prices')->updateOrInsert(
        [
            'user_id' => $user->id,
            'item_id' => $id,
            'party_id' => $data['party_id'],
        ],
        [
            'sales_price' => $data['sales_price'],
            'updated_at' => now(),
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Party wise price saved successfully',
    ]);
}


}
