<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'user_id',
        'party_id',
        'proforma_number',
        'proforma_date',
        'expiry_date',
        'validity_days',
        'subtotal',
        'total_tax',
        'additional_charges_total',
        'discount_percent',
        'discount_amount',
        'round_off',
        'grand_total',
        'notes',
        'status',
        'converted_invoice_id',
    ];

    protected $casts = [
        'proforma_date' => 'date',
        'expiry_date' => 'date',
        'validity_days' => 'integer',
        'subtotal' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'additional_charges_total' => 'decimal:2',
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
        return $this->hasMany(ProformaInvoiceItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(
            ProformaInvoiceAdditionalCharge::class
        );
    }

    public function convertedInvoice()
    {
        return $this->belongsTo(
            Invoice::class,
            'converted_invoice_id'
        );
    }
}
