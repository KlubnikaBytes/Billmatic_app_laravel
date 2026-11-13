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
        Schema::create('credit_note_items', function (Blueprint $table) {
              $table->id();

            $table->foreignId('credit_note_id')
                ->constrained('credit_notes')
                ->cascadeOnDelete();

            // Nullable for manually added items.
            $table->foreignId('invoice_item_id')
                ->nullable()
                ->constrained('invoice_items')
                ->restrictOnDelete();

            $table->foreignId('item_id')
                ->constrained('items')
                ->restrictOnDelete();

            $table->text('description')->nullable();

            $table->decimal('qty', 15, 3);
            $table->string('unit', 50)->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('gst_percent', 8, 2)->default(0);
            $table->decimal('gst_amount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);

            $table->timestamps();

            $table->index('credit_note_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_note_items');
    }
};
