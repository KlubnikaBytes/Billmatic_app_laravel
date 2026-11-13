<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'user_id',
        'party_id',
        'purchase_id',
        'purchase_return_number',
        'return_date',
        'place_of_supply',
        'subtotal',
        'total_tax',
        'discount_percent',
        'discount_amount',
        'round_off',
        'grand_total',
        'received_amount',
        'balance_amount',
        'payment_mode',
        'status',
        'notes',
    ];

    protected $casts = [
        'return_date' => 'date:Y-m-d',
        'subtotal' => 'float',
        'total_tax' => 'float',
        'discount_percent' => 'float',
        'discount_amount' => 'float',
        'round_off' => 'float',
        'grand_total' => 'float',
        'received_amount' => 'float',
        'balance_amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    public function additionalCharges(): HasMany
    {
        return $this->hasMany(PurchaseReturnAdditionalCharge::class);
    }
}
