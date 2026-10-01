<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city',
        'billing_requirement',
        'language',
        'business_type',
        'industry',
        'gst_registered',
        'gst_number',
        'pan_cin',       // 🔥 ADD HERE
        'trade_license_or_udyam',  // 🔥 ADD THIS
        'business_name',
        'business_address',
        'gst_status',
        'gst_legal_name',

        'invoice_format',
    ];

    protected $casts = [
        'business_type' => 'array',   // 🟣 VERY IMPORTANT
        'gst_registered' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
