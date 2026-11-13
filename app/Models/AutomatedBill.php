<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomatedBill extends Model
{
    protected $fillable = [
        'user_id', 'party_id',
        'start_date', 'end_date',
        'repeat_every', 'frequency',
        'due_days', 'next_billing_date',
        'last_billing_date', 'status',
        'subtotal', 'total_tax',
        'discount_percent', 'discount_amount',
        'round_off', 'tcs_amount',
        'grand_total', 'received_amount',
        'payment_mode', 'notes', 'send_sms',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'last_billing_date' => 'date',
        'send_sms' => 'boolean',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function items()
    {
        return $this->hasMany(AutomatedBillItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(
            AutomatedBillAdditionalCharge::class
        );
    }

    public function runs()
    {
        return $this->hasMany(AutomatedBillRun::class);
    }
}
