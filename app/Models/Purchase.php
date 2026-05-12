<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'purchase_number',
        'purchase_date',
        'due_date',
        'place_of_supply', // ✅ ADD THIS
        'party_id',
        'subtotal',
        'total_tax',
        'discount_percent',
        'discount_amount',
        'round_off',
        'tcs_amount',
        'received_amount',
        'balance_amount',
        'payment_mode',
        'status',
        'grand_total',
        'notes',
    ];

    // ================= RELATIONS =================

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(PurchaseAdditionalCharge::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
