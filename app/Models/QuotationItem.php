<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'item_id',
        'description',
        'qty',
        'unit',
        'price',
        'discount',
        'gst_percent',
        'gst_amount',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
