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
    Schema::create('managed_users', function (Blueprint $table) {
        $table->id();

        $table->foreignId('owner_user_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->string('role', 30);
        $table->string('name');
        $table->string('phone', 10);
        $table->string('email');

        $table->timestamps();

        $table->unique(['owner_user_id', 'phone']);
        $table->unique(['owner_user_id', 'email']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managed_users');
    }
};
