<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturnItem extends Model
{
    protected $fillable = [
        'purchase_return_id',
        'item_id',
        'description',
        'hsn',
        'qty',
        'unit',
        'price',
        'discount',
        'gst_percent',
        'gst_amount',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'float',
        'price' => 'float',
        'discount' => 'float',
        'gst_percent' => 'float',
        'gst_amount' => 'float',
        'line_total' => 'float',
    ];

    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
