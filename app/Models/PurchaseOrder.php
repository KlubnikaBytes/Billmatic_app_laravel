<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseOrder extends Model { protected $table = 'purchase_orders'; protected $fillable = ['user_id','party_id','order_number','order_date','expiry_date','subtotal','tax_total','charges_total','discount_percent','discount_amount','round_off','grand_total','notes','status','converted_purchase_id']; public function items(){return $this->hasMany(PurchaseOrderItem::class);} public function charges(){return $this->hasMany(PurchaseOrderCharge::class);} public function party(){return $this->belongsTo(Party::class);} }
