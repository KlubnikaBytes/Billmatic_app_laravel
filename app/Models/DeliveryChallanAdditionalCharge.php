<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallanAdditionalCharge extends Model
{
    protected $fillable = [
        'delivery_challan_id',
        'name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function deliveryChallan()
    {
        return $this->belongsTo(DeliveryChallan::class);
    }
}
