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
        Schema::create('automated_bills', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('party_id')->constrained()->restrictOnDelete();

        $table->date('start_date');
        $table->date('end_date');

        $table->unsignedInteger('repeat_every')->default(1);
        $table->enum('frequency', ['day', 'week', 'month', 'year'])
              ->default('month');

        $table->unsignedInteger('due_days')->default(30);

        $table->date('next_billing_date');
        $table->date('last_billing_date')->nullable();

        $table->enum('status', ['active', 'paused', 'stopped'])
              ->default('active');

        $table->decimal('subtotal', 14, 2)->default(0);
        $table->decimal('total_tax', 14, 2)->default(0);
        $table->decimal('discount_percent', 8, 2)->default(0);
        $table->decimal('discount_amount', 14, 2)->default(0);
        $table->decimal('round_off', 14, 2)->default(0);
        $table->decimal('tcs_amount', 14, 2)->default(0);
        $table->decimal('grand_total', 14, 2)->default(0);

        $table->decimal('received_amount', 14, 2)->default(0);
        $table->string('payment_mode', 50)->nullable();

        $table->text('notes')->nullable();
        $table->boolean('send_sms')->default(false);

        $table->timestamps();

        $table->index(['status', 'next_billing_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automated_bills');
    }
};
