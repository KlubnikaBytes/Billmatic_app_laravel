<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InvoiceItem;
use App\Models\Invoice;

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

        $data['user_id'] = $user->id;

        // 📸 Image upload
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('items', 'public');
        $data['image_path'] = $path;
    }

    // ✅ Checkbox safe handling
    $data['show_in_online_store'] = $request->boolean('show_in_online_store');

    
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

    if ($request->hasFile('image')) {
        $data['image_path'] = $request->file('image')->store('items', 'public');
    }

    $data['show_in_online_store'] = $request->boolean('show_in_online_store');

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


// ✅ ADD THIS METHOD (REQUIRED)
private function calculateStockValue($qty, $purchasePrice)
{
    $qty = (float) $qty;
    $purchasePrice = (float) $purchasePrice;

    return round($qty * $purchasePrice, 2);
}

}


