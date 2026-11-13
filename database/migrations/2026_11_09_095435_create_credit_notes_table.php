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
        Schema::create('credit_notes', function (Blueprint $table) {
              $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Optional: credit note can be created without an invoice.
            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->restrictOnDelete();

            $table->foreignId('party_id')
                ->constrained('parties')
                ->restrictOnDelete();

            $table->string('credit_note_number', 50);
            $table->date('credit_note_date');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('discount_percent', 8, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('tcs_amount', 15, 2)->default(0);

            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);

            $table->string('payment_mode', 50)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['user_id', 'credit_note_number'],
                'credit_note_user_number_unique'
            );

            $table->index(['user_id', 'credit_note_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
