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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_return_id')
                ->constrained('sales_returns')
                ->cascadeOnDelete();

            $table->foreignId('invoice_item_id')
                ->nullable()
                ->constrained('invoice_items')
                ->restrictOnDelete();

            $table->foreignId('item_id')
                ->constrained('items')
                ->restrictOnDelete();

            $table->text('description')->nullable();

            $table->decimal('qty', 15, 3);
            $table->string('unit', 50)->nullable();

            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);

            $table->decimal('gst_percent', 8, 2)->default(0);
            $table->decimal('gst_amount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);

            $table->timestamps();

            $table->index('sales_return_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};
