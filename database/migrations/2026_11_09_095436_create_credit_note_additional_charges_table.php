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
        Schema::create('credit_note_additional_charges', function (Blueprint $table) {
             $table->id();

                $table->foreignId('credit_note_id')
                    ->constrained('credit_notes')
                    ->cascadeOnDelete();

                $table->string('name', 255);
                $table->decimal('amount', 15, 2)->default(0);

                $table->timestamps();

                $table->index('credit_note_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_note_additional_charges');
    }
};
