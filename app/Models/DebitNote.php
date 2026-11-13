<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebitNote extends Model
{
    protected $fillable = [
        'user_id',
        'debit_note_number',
        'debit_note_date',
        'party_id',
        'purchase_id',
        'subtotal',
        'total_tax',
        'discount_percent',
        'discount_amount',
        'round_off',
        'tcs_amount',
        'tds_amount',
        'received_amount',
        'balance_amount',
        'payment_mode',
        'grand_total',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function items()
    {
        return $this->hasMany(DebitNoteItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(DebitNoteAdditionalCharge::class);
    }
}
