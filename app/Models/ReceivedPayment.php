<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class ReceivedPayment extends Model
{
    protected $fillable = [
        'user_id',
        'party_id',
        'payment_date',
        'payment_number',
        'amount',
        'payment_mode'
    ];

    public function party()
{
    return $this->belongsTo(Party::class);
}

public function invoices()
{
    return $this->belongsToMany(
        Invoice::class,
        'received_payment_invoice',
        'received_payment_id',
        'invoice_id'
    )->withPivot('amount');
}

}
