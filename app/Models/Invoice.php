<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'party_id',
        'place_of_supply',
        'subtotal',
        'total_tax',
        'grand_total',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
        'subtotal'     => 'float',
        'total_tax'    => 'float',
        'grand_total'  => 'float',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
