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
        Schema::create('automated_bill_additional_charges', function (Blueprint $table) {
            $table->id();

        $table->foreignId('automated_bill_id')
              ->constrained('automated_bills')
              ->cascadeOnDelete();

        $table->string('name');
        $table->decimal('amount', 14, 2)->default(0);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automated_bill_additional_charges');
    }
};
