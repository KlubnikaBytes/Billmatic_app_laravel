<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    protected $fillable = [
        'credit_note_id',
        'invoice_item_id',
        'item_id',
        'description',
        'qty',
        'unit',
        'price',
        'discount',
        'gst_percent',
        'gst_amount',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
