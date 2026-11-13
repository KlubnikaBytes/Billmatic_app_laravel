<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItemCatalog extends Model
{
    use HasFactory;

    protected $table = 'expense_item_catalog';

    protected $fillable = [
        'user_id',
        'item_name',
        'item_type',
        'purchase_price',
        'price_type',
        'unit',
        'gst_percent',
        'hsn_code',
        'itc_applicable',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'gst_percent' => 'decimal:2',
    ];
}
