<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{PurchaseOrder,Party,Item};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class PurchaseOrderController extends Controller {
 public function nextNumber(Request $r) {
  $numbers=PurchaseOrder::where('user_id',$r->user()->id)->pluck('order_number');
  $max=0; foreach($numbers as $n){ if(ctype_digit((string)$n)) $max=max($max,(int)$n); }
  return response()->json(['success'=>true,'next_number'=>(string)($max+1)]);
 }
 public function index(Request $r) { return PurchaseOrder::where('user_id',$r->user()->id)->with('party')->latest()->paginate(30); }
 public function show(Request $r,$id) {
  $order=PurchaseOrder::where('user_id',$r->user()->id)->with(['party','items','charges'])->findOrFail($id);
  return response()->json(['success'=>true,'data'=>$order]);
 }
 public function store(Request $r) {
  $d=$r->validate([
   'order_number'=>'required|string|max:100','order_date'=>'required|date','expiry_date'=>'nullable|date|after_or_equal:order_date',
   'party_id'=>'required|integer','notes'=>'nullable|string|max:5000',
   'discount_percent'=>'nullable|numeric|between:0,100','round_off'=>'nullable|numeric|between:-1000000,1000000',
   'items'=>'required|array|min:1','items.*.item_id'=>'required|integer',
   'items.*.description'=>'nullable|string|max:500','items.*.qty'=>'required|numeric|gt:0',
   'items.*.unit'=>'nullable|string|max:40','items.*.price'=>'required|numeric|min:0',
   'items.*.discount'=>'nullable|numeric|min:0','items.*.gst_percent'=>'nullable|numeric|between:0,100',
   'additional_charges'=>'nullable|array','additional_charges.*.name'=>'required|string|max:255',
   'additional_charges.*.amount'=>'required|numeric|min:0',
  ]);
  $uid=$r->user()->id;
  if(!Party::where('user_id',$uid)->whereKey($d['party_id'])->exists()) throw ValidationException::withMessages(['party_id'=>'Party not found for this user.']);
  if(PurchaseOrder::where('user_id',$uid)->where('order_number',$d['order_number'])->exists()) throw ValidationException::withMessages(['order_number'=>'Order number already exists.']);
  $lines=[]; $subtotal=0; $tax=0;
  foreach($d['items'] as $line){
   if(!Item::where('user_id',$uid)->whereKey($line['item_id'])->exists()) throw ValidationException::withMessages(['items'=>'An item does not belong to this user.']);
   $base=round($line['qty']*$line['price'],2); $disc=round($line['discount']??0,2);
   if($disc>$base) throw ValidationException::withMessages(['items'=>'Item discount exceeds item amount.']);
   $taxable=$base-$disc; $gst=round($taxable*($line['gst_percent']??0)/100,2);
   $subtotal+=$taxable; $tax+=$gst;
   $lines[]=['item_id'=>$line['item_id'],'description'=>$line['description']??null,'qty'=>$line['qty'],'unit'=>$line['unit']??null,'price'=>$line['price'],'discount'=>$disc,'gst_percent'=>$line['gst_percent']??0,'gst_amount'=>$gst,'line_total'=>round($taxable+$gst,2)];
  }
  $charges=collect($d['additional_charges']??[]); $chargeTotal=round($charges->sum('amount'),2);
  $beforeDiscount=round($subtotal+$tax+$chargeTotal,2);
  $percent=$d['discount_percent']??0; $discount=round($beforeDiscount*$percent/100,2);
  $roundOff=$d['round_off']??0; $grand=round($beforeDiscount-$discount+$roundOff,2);
  if($grand<0) throw ValidationException::withMessages(['round_off'=>'Total cannot be negative.']);
  $order=DB::transaction(function()use($d,$uid,$subtotal,$tax,$chargeTotal,$percent,$discount,$roundOff,$grand,$lines,$charges){
   $o=PurchaseOrder::create(['user_id'=>$uid,'party_id'=>$d['party_id'],'order_number'=>$d['order_number'],'order_date'=>$d['order_date'],'expiry_date'=>$d['expiry_date']??null,'notes'=>$d['notes']??null,'subtotal'=>round($subtotal,2),'tax_total'=>round($tax,2),'charges_total'=>$chargeTotal,'discount_percent'=>$percent,'discount_amount'=>$discount,'round_off'=>$roundOff,'grand_total'=>$grand]);
   $o->items()->createMany($lines); $o->charges()->createMany($charges->all()); return $o;
  });
  return response()->json(['success'=>true,'data'=>$order->load(['party','items','charges'])],201);
 }
 public function destroy(Request $r,$id) {
  $o=PurchaseOrder::where('user_id',$r->user()->id)->findOrFail($id);
  if($o->converted_purchase_id) return response()->json(['message'=>'Converted orders cannot be deleted.'],409);
  $o->delete(); return response()->json(['success'=>true]);
 }
}
