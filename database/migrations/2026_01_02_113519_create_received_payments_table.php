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
        // Schema::create('received_payments', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

    Schema::create('received_payments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('party_id');
    $table->date('payment_date');
    $table->string('payment_number');
    $table->decimal('amount', 10, 2);
    $table->string('payment_mode');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('party_id')->references('id')->on('parties')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_payments');
    }
};
