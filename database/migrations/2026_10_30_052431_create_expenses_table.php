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
        Schema::create('expenses', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('expense_number');
            $table->string('original_invoice_number')->nullable();

            $table->foreignId('expense_category_id')
                ->nullable()
                ->constrained('expense_categories')
                ->nullOnDelete();

            $table->date('expense_date');

            $table->boolean('is_gst')->default(false);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('gst_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->string('payment_mode')->default('Cash');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'expense_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
