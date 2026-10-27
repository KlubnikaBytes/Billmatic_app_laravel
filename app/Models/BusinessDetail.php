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
        'business_address',
        
        'business_phone_number',
        'show_phone_on_invoice',

        'business_state',
        'business_pincode',
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
        'business_email', // ADD THIS
        'gst_status',
        'gst_legal_name',

        'invoice_format',
        'discount_type',

        'invoice_number_prefix',
        'starting_serial_number',

        'invoice_theme',
        'invoice_template',
        'invoice_theme_color',

         // Add this:
       'custom_fields',
       'terms_invoice_type',
       'terms_and_conditions',
       'signature_image',
       'show_empty_signature_box',
       'enable_receiver_signature',
    ];

    protected $casts = [
        'business_type' => 'array',   // 🟣 VERY IMPORTANT
        'gst_registered' => 'boolean',
        
        'show_phone_on_invoice' => 'boolean',

        'show_empty_signature_box' => 'boolean',
        'enable_receiver_signature' => 'boolean',

        // Add this:
        'custom_fields' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
