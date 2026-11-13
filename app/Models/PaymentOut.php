<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOut extends Model
{
     protected $fillable = [
        'user_id',
        'party_id',
        'payment_number',
        'payment_date',
        'amount',
        'discount_amount',
        'payment_mode',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function allocations()
    {
        return $this->hasMany(PaymentOutAllocation::class);
    }
}
