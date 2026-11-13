<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'party_id',
        'sales_return_number',
        'sales_return_date',
        'subtotal',
        'total_tax',
        'discount_percent',
        'discount_amount',
        'round_off',
        'tcs_amount',
        'paid_amount',
        'balance_amount',
        'payment_mode',
        'grand_total',
        'notes',
    ];

    protected $casts = [
        'sales_return_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(SalesReturnAdditionalCharge::class);
    }
}
