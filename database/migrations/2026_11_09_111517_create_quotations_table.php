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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('party_id')
                ->constrained('parties')
                ->restrictOnDelete();

            $table->string('quotation_number', 50);
            $table->date('quotation_date');
            $table->date('expiry_date');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);

            $table->decimal('discount_percent', 8, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);

            $table->decimal('grand_total', 15, 2)->default(0);

            $table->text('notes')->nullable();

            $table->string('status', 30)->default('draft');

            $table->foreignId('converted_invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['user_id', 'quotation_number'],
                'quotations_user_number_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
