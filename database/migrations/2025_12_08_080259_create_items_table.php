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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('name');
            $table->enum('item_type', ['product', 'service'])->default('product');

            // Pricing
            $table->string('inventory_tracking_by')->nullable(); // e.g. "Qty", "None"
            $table->string('unit')->nullable(); // PCS, BOX etc.
            $table->decimal('sales_price', 15, 2)->default(0);
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->string('hsn_code')->nullable();

            // Stock
            $table->decimal('opening_stock', 15, 2)->default(0);
            $table->date('stock_as_of_date')->nullable();
            $table->string('item_code')->nullable();
            $table->string('barcode')->nullable();

            // Other
            $table->unsignedBigInteger('item_category_id')->nullable();
            $table->string('image_path')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('item_category_id')->references('id')->on('item_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
