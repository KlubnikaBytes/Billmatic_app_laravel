<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAdditionalCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'charge_name',
        'amount',
        'tax_percent',
        'tax_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
