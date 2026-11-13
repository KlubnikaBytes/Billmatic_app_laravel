<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturnAdditionalCharge extends Model
{
    protected $fillable = [
        'purchase_return_id',
        'name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class);
    }
}
