<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebitNoteAdditionalCharge extends Model
{
    protected $fillable = ['name', 'amount'];

    public function debitNote()
    {
        return $this->belongsTo(DebitNote::class);
    }
}
