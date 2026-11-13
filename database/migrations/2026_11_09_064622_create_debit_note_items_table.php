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
    Schema::create('debit_note_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('debit_note_id')
            ->constrained('debit_notes')
            ->cascadeOnDelete();
        $table->foreignId('item_id')->nullable();
        $table->string('description')->nullable();
        $table->decimal('qty', 12, 3);
        $table->string('unit', 40)->nullable();
        $table->decimal('price', 15, 2)->default(0);
        $table->decimal('discount', 15, 2)->default(0);
        $table->decimal('gst_percent', 5, 2)->default(0);
        $table->decimal('gst_amount', 15, 2)->default(0);
        $table->decimal('line_total', 15, 2)->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_note_items');
    }
};
