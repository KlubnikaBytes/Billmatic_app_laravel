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
        Schema::create('proforma_invoice_items', function (Blueprint $table) {
             $table->id();

            $table->foreignId('proforma_invoice_id')
                ->constrained('proforma_invoices')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('item_id')->nullable();

            $table->string('item_name');
            $table->string('hsn_code', 50)->nullable();
            $table->string('unit', 30)->nullable();

            $table->decimal('quantity', 15, 3)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_invoice_items');
    }
};
