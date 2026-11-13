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
    Schema::create('debit_notes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('party_id')->constrained();
        $table->foreignId('purchase_id')->nullable()->constrained('purchases');
        $table->string('debit_note_number', 100);
        $table->date('debit_note_date');
        $table->decimal('subtotal', 15, 2)->default(0);
        $table->decimal('total_tax', 15, 2)->default(0);
        $table->decimal('discount_percent', 5, 2)->default(0);
        $table->decimal('discount_amount', 15, 2)->default(0);
        $table->decimal('round_off', 15, 2)->default(0);
        $table->decimal('tcs_amount', 15, 2)->default(0);
        $table->decimal('tds_amount', 15, 2)->default(0);
        $table->decimal('received_amount', 15, 2)->default(0);
        $table->decimal('balance_amount', 15, 2)->default(0);
        $table->string('payment_mode')->nullable();
        $table->decimal('grand_total', 15, 2)->default(0);
        $table->text('notes')->nullable();
        $table->timestamps();

        $table->unique(['user_id', 'debit_note_number']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_notes');
    }
};
