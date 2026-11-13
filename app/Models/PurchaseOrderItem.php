<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseOrderItem extends Model { protected $table = 'purchase_order_items'; protected $fillable = ['purchase_order_id','item_id','description','qty','unit','price','discount','gst_percent','gst_amount','line_total'];  }
