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
        Schema::create('item_stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->enum('type', ['add', 'reduce']);
            $table->decimal('quantity', 15, 2);
            $table->decimal('stock_before', 15, 2);
            $table->decimal('stock_after', 15, 2);
            $table->date('adjustment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'item_id', 'adjustment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_stock_adjustments');
    }
};
