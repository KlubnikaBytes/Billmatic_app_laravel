<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomatedBillItem extends Model
{
    protected $fillable = [
        'automated_bill_id',
        'item_id',
        'description',
        'qty',
        'unit',
        'price',
        'discount',
        'gst_percent',
        'gst_amount',
        'line_total',
    ];

    public function automatedBill()
    {
        return $this->belongsTo(AutomatedBill::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
