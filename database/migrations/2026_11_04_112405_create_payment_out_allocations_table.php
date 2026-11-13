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
        Schema::create('payment_out_allocations', function (Blueprint $table) {
             $table->id();

            $table->foreignId('payment_out_id')
                ->constrained('payment_outs')
                ->cascadeOnDelete();

            $table->foreignId('purchase_id')
                ->constrained('purchases')
                ->cascadeOnDelete();

            $table->decimal('amount', 15, 2)->default(0);

            $table->decimal('discount_amount', 15, 2)
                ->default(0);

            $table->timestamps();

            $table->unique(
                ['payment_out_id', 'purchase_id'],
                'payment_out_purchase_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_out_allocations');
    }
};
