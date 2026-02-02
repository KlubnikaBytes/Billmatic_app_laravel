<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'item_type',
        'inventory_tracking_by',
        'unit',
        'sales_price',
        'purchase_price',
        'gst_percent',
        'hsn_code',
        'opening_stock',
        'stock_value',
        'stock_as_of_date',
        'item_code',
        'barcode',
        'item_categories', // ✅ NEW
        'image_path',
        'custom_fields',
        'description',
        'imei_list',  // <-- NEW
        'low_stock_alert',
        'low_stock_quantity',
        'show_in_online_store', // ✅ NEW

    ];

    protected $casts = [
        'sales_price'      => 'float',
        'purchase_price'   => 'float',
        'gst_percent'      => 'float',
        'opening_stock'    => 'float',
        'stock_value'        => 'float',   // ✅ ADD THIS
        'stock_as_of_date' => 'date',
        'custom_fields'    => 'array',
        'imei_list'        => 'array',   // <-- NEW
        'item_categories'  => 'array', // ✅ NEW
        'low_stock_alert' => 'boolean',
        'low_stock_quantity' => 'float',
        'show_in_online_store'   => 'boolean', // ✅ NEW

    ];





    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
