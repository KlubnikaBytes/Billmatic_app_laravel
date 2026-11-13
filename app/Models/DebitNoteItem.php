<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebitNoteItem extends Model
{
    protected $fillable = [
        'item_id', 'description', 'qty', 'unit',
        'price', 'discount', 'gst_percent',
        'gst_amount', 'line_total',
    ];

    public function debitNote()
    {
        return $this->belongsTo(DebitNote::class);
    }
}
