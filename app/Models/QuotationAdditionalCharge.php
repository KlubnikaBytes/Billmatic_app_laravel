<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationAdditionalCharge extends Model
{
    protected $fillable = [
        'quotation_id',
        'name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
