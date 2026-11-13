<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnAdditionalCharge extends Model
{
    protected $fillable = [
        'sales_return_id',
        'name',
        'amount',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }
}
