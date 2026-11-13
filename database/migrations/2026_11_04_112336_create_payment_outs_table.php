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
        Schema::create('payment_outs', function (Blueprint $table) {
             $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('party_id')
                ->constrained('parties')
                ->cascadeOnDelete();

            $table->unsignedInteger('payment_number');

            $table->date('payment_date');

            // Actual amount paid
            $table->decimal('amount', 15, 2);

            // Discount given to supplier
            $table->decimal('discount_amount', 15, 2)
                ->default(0);

            $table->string('payment_mode')->default('Cash');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['user_id', 'payment_number'],
                'payment_out_user_number_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_outs');
    }
};
