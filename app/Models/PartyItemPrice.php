<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyItemPrice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'party_id', 'item_id', 'price'];

    public function party()  { return $this->belongsTo(Party::class); }
    public function item()   { return $this->belongsTo(Item::class);  }
}
