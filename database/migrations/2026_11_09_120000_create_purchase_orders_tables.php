<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('purchase_orders', function(Blueprint $t) {
   $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
   $t->foreignId('party_id')->constrained()->restrictOnDelete();
   $t->string('order_number'); $t->date('order_date'); $t->date('expiry_date')->nullable();
   $t->decimal('subtotal',14,2); $t->decimal('tax_total',14,2); $t->decimal('charges_total',14,2);
   $t->decimal('discount_percent',5,2)->default(0); $t->decimal('discount_amount',14,2)->default(0);
   $t->decimal('round_off',14,2)->default(0); $t->decimal('grand_total',14,2);
   $t->text('notes')->nullable(); $t->string('status')->default('open'); $t->foreignId('converted_purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
   $t->timestamps(); $t->unique(['user_id','order_number']);
  });
  Schema::create('purchase_order_items',function(Blueprint $t){
   $t->id(); $t->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
   $t->foreignId('item_id')->constrained()->restrictOnDelete(); $t->string('description')->nullable();
   $t->decimal('qty',14,3); $t->string('unit')->nullable(); $t->decimal('price',14,2);
   $t->decimal('discount',14,2)->default(0); $t->decimal('gst_percent',6,2)->default(0);
   $t->decimal('gst_amount',14,2); $t->decimal('line_total',14,2); $t->timestamps();
  });
  Schema::create('purchase_order_charges',function(Blueprint $t){
   $t->id(); $t->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
   $t->string('name'); $t->decimal('amount',14,2); $t->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('purchase_order_charges'); Schema::dropIfExists('purchase_order_items'); Schema::dropIfExists('purchase_orders'); }
};
