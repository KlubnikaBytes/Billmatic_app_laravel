<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoiceAdditionalCharge extends Model
{
    protected $fillable = [
        'proforma_invoice_id',
        'name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }
}
