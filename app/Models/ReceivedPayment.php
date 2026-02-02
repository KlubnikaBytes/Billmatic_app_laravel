<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivedPayment extends Model
{
    protected $fillable = [
        'user_id',
        'party_id',
        'payment_date',
        'payment_number',
        'amount',
        'payment_mode'
    ];

    public function party()
{
    return $this->belongsTo(Party::class);
}

}
