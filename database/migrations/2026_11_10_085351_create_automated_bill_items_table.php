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
        Schema::create('automated_bill_items', function (Blueprint $table) {
           $table->id();

        $table->foreignId('automated_bill_id')
              ->constrained('automated_bills')
              ->cascadeOnDelete();

        $table->foreignId('item_id')
              ->constrained('items')
              ->restrictOnDelete();

        $table->text('description')->nullable();
        $table->decimal('qty', 14, 3);
        $table->string('unit', 50)->default('PCS');
        $table->decimal('price', 14, 2);
        $table->decimal('discount', 14, 2)->default(0);
        $table->decimal('gst_percent', 8, 2)->default(0);
        $table->decimal('gst_amount', 14, 2)->default(0);
        $table->decimal('line_total', 14, 2)->default(0);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automated_bill_items');
    }
};
