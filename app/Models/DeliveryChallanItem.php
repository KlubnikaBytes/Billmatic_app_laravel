<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallanItem extends Model
{
    protected $fillable = [
        'delivery_challan_id',
        'item_id',
        'item_name',
        'hsn_code',
        'unit',
        'quantity',
        'rate',
        'tax_percent',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'rate' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function deliveryChallan()
    {
        return $this->belongsTo(DeliveryChallan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
