<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteAdditionalCharge extends Model
{
    protected $fillable = [
        'credit_note_id',
        'name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }
}
