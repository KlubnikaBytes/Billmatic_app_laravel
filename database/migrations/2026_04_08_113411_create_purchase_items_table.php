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
    Schema::create('purchase_items', function (Blueprint $table) {
        $table->id();

        $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
        $table->foreignId('item_id')->constrained()->cascadeOnDelete();

        $table->string('description')->nullable();

        $table->decimal('qty', 10, 2);
        $table->string('unit')->nullable();

        $table->decimal('price', 10, 2);
        $table->decimal('discount', 10, 2)->default(0);

        $table->decimal('gst_percent', 5, 2)->default(0);
        $table->decimal('gst_amount', 10, 2)->default(0);

        $table->decimal('line_total', 12, 2);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
