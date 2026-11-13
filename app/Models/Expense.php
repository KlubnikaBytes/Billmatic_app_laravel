<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'party_id',
        'party_name',
        'expense_number',
        'original_invoice_number',
        'expense_category_id',
        'expense_date',
        'is_gst',
        'subtotal',
        'gst_amount',
        'additional_charges',
        'discount',
        'discount_percentage',
        'total_amount',
        'status',
        'payment_mode',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'is_gst' => 'boolean',
        'subtotal' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'discount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function party()
    {
    return $this->belongsTo(\App\Models\Party::class, 'party_id');
    }

    public function additionalCharges()
{
    return $this->hasMany(ExpenseAdditionalCharge::class);
}
}
