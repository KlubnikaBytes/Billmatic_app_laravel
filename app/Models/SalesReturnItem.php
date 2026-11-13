<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $fillable = [
        'sales_return_id',
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

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
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
