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
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->index();
            $table->foreignId('party_id')->index();

            $table->string('proforma_number', 100);
            $table->date('proforma_date');
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('validity_days')->default(7);

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('additional_charges_total', 15, 2)->default(0);
            $table->decimal('discount_percent', 8, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->text('notes')->nullable();

            $table->string('status', 30)->default('created');
            $table->unsignedBigInteger('converted_invoice_id')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'proforma_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_invoices');
    }
};
