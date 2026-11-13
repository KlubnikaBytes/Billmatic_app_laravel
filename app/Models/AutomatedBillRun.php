<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomatedBillRun extends Model
{
    protected $fillable = [
        'automated_bill_id',
        'billing_date',
        'invoice_id',
        'status',
        'error_message',
    ];

    protected $casts = [
        'billing_date' => 'date',
    ];

    public function automatedBill()
    {
        return $this->belongsTo(AutomatedBill::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
