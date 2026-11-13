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
        Schema::create('delivery_challan_items', function (Blueprint $table) {
              $table->id();

            $table->foreignId('delivery_challan_id')
                ->constrained('delivery_challans')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('item_id')->nullable();

            $table->string('item_name');
            $table->string('hsn_code')->nullable();
            $table->string('unit', 30)->default('PCS');

            $table->decimal('quantity', 15, 3);
            $table->decimal('rate', 15, 2);

            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->timestamps();

            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_challan_items');
    }
};
