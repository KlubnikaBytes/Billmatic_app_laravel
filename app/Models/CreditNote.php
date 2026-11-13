<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'party_id',
        'credit_note_number',
        'credit_note_date',
        'subtotal',
        'total_tax',
        'discount_percent',
        'discount_amount',
        'round_off',
        'tcs_amount',
        'grand_total',
        'paid_amount',
        'balance_amount',
        'payment_mode',
        'notes',
    ];

    protected $casts = [
        'credit_note_date' => 'date',
        'subtotal' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'tcs_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(CreditNoteAdditionalCharge::class);
    }
}
