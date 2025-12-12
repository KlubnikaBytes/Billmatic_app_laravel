<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $items = Item::where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'                 => 'required|string|max:255',
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

            'item_category_id'     => 'nullable|exists:item_categories,id',
            'description'          => 'nullable|string',
            'custom_fields'        => 'nullable|array',
             'imei_list'            => 'nullable|array',   // <-- NEW
        ]);

        $data['user_id'] = $user->id;

        $item = Item::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
            'data'    => $item,
        ], 201);
    }
}
