<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_item_catalog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->string('item_type')->default('Product');
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->string('price_type')->default('With Tax');
            $table->string('unit', 50)->default('PCS');
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->string('hsn_code', 50)->nullable();
            $table->string('itc_applicable')->default('Eligible For ITC');
            $table->timestamps();

            $table->index(['user_id', 'item_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_item_catalog');
    }
};
