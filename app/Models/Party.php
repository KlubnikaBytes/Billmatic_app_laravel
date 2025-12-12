<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'party_name',
        'contact_number',
        'party_type',
        'gst_number',
        'pan_number',
        'billing_street',
        'billing_state',
        'billing_pincode',
        'billing_city',
        'opening_balance',
        'credit_period_days',
        'credit_limit',
        'party_category_id',
        'contact_person_name',
        'dob',
    ];

    protected $casts = [
        'opening_balance'   => 'float',
        'credit_limit'      => 'float',
        'dob'               => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(PartyCategory::class, 'party_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
