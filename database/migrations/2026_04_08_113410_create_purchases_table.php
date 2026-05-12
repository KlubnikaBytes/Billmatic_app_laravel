<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('purchases', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $table->string('purchase_number')->unique();
        $table->date('purchase_date');
        $table->date('due_date')->nullable();

        $table->foreignId('party_id')->constrained()->cascadeOnDelete();

        $table->decimal('subtotal', 12, 2)->default(0);
        $table->decimal('total_tax', 12, 2)->default(0);

        $table->decimal('discount_percent', 5, 2)->default(0);
        $table->decimal('discount_amount', 12, 2)->default(0);

        $table->decimal('round_off', 12, 2)->default(0);
        $table->decimal('tcs_amount', 12, 2)->default(0);

        $table->decimal('received_amount', 12, 2)->default(0);
        $table->decimal('balance_amount', 12, 2)->default(0);

        $table->string('payment_mode')->nullable();
        $table->string('status')->default('unpaid');

        $table->decimal('grand_total', 12, 2);

        $table->text('notes')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
