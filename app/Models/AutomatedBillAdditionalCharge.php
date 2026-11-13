<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomatedBillAdditionalCharge extends Model
{
    protected $fillable = [
        'automated_bill_id',
        'name',
        'amount',
    ];

    public function automatedBill()
    {
        return $this->belongsTo(AutomatedBill::class);
    }
}
