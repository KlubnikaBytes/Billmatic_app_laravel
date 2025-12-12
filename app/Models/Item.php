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
        'stock_as_of_date',
        'item_code',
        'barcode',
        'item_category_id',
        'image_path',
        'custom_fields',
        'description',
        'imei_list',  // <-- NEW
    ];

    protected $casts = [
        'sales_price'      => 'float',
        'purchase_price'   => 'float',
        'gst_percent'      => 'float',
        'opening_stock'    => 'float',
        'stock_as_of_date' => 'date',
        'custom_fields'    => 'array',
         'imei_list'        => 'array',   // <-- NEW
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
