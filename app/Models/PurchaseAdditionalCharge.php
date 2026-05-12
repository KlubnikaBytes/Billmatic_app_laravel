<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseAdditionalCharge extends Model
{
    protected $fillable = [
        'purchase_id',
        'name',
        'amount',
    ];

    // ================= RELATIONS =================

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}