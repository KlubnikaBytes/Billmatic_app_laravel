<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'user_id',
        'party_id',
        'quotation_number',
        'quotation_date',
        'expiry_date',
        'subtotal',
        'total_tax',
        'discount_percent',
        'discount_amount',
        'round_off',
        'grand_total',
        'notes',
        'status',
        'converted_invoice_id',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'subtotal' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(QuotationAdditionalCharge::class);
    }

    public function convertedInvoice()
    {
        return $this->belongsTo(
            Invoice::class,
            'converted_invoice_id'
        );
    }
}
