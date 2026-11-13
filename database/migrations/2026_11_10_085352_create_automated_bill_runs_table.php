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
        Schema::create('automated_bill_runs', function (Blueprint $table) {
            $table->id();

        $table->foreignId('automated_bill_id')
              ->constrained('automated_bills')
              ->cascadeOnDelete();

        $table->date('billing_date');

        $table->foreignId('invoice_id')
              ->nullable()
              ->constrained('invoices')
              ->nullOnDelete();

        $table->enum('status', ['pending', 'completed', 'failed'])
              ->default('pending');

        $table->text('error_message')->nullable();

        $table->timestamps();

        $table->unique(
            ['automated_bill_id', 'billing_date'],
            'automated_bill_unique_billing_run'
        );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automated_bill_runs');
    }
};
