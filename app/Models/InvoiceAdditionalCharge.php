<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceAdditionalCharge extends Model
{
      protected $fillable = [
        'invoice_id',
        'name',
        'amount',
    ];
}
