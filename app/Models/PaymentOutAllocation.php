<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOutAllocation extends Model
{
    protected $fillable = [
        'payment_out_id',
        'purchase_id',
        'amount',
        'discount_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function paymentOut()
    {
        return $this->belongsTo(PaymentOut::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
