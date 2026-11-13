<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallan extends Model
{
    protected $fillable = [
        'user_id',
        'party_id',
        'challan_number',
        'challan_date',
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
        'challan_date' => 'date',
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
        return $this->hasMany(DeliveryChallanItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(
            DeliveryChallanAdditionalCharge::class
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
