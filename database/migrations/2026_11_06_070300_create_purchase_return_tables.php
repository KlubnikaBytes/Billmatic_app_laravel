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
       Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('parties')->restrictOnDelete();
            $table->foreignId('purchase_id')->constrained('purchases')->restrictOnDelete();
            $table->string('purchase_return_number', 50);
            $table->date('return_date');
            $table->string('place_of_supply', 100)->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('discount_percent', 8, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('received_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            $table->string('payment_mode', 50)->nullable();
            $table->string('status', 20)->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['user_id', 'purchase_return_number'],
                'purchase_returns_user_number_unique'
            );
            $table->index(['user_id', 'return_date']);
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')
                ->constrained('purchase_returns')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->nullable()
                ->constrained('items')
                ->nullOnDelete();
            $table->string('description')->nullable();
            $table->string('hsn', 50)->nullable();
            $table->decimal('qty', 15, 3);
            $table->string('unit', 50)->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('gst_percent', 8, 2)->default(0);
            $table->decimal('gst_amount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['purchase_return_id', 'item_id']);
        });

        Schema::create('purchase_return_additional_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')
                ->constrained('purchase_returns')
                ->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_additional_charges');
    Schema::dropIfExists('purchase_return_items');
    Schema::dropIfExists('purchase_returns');
    }
};
