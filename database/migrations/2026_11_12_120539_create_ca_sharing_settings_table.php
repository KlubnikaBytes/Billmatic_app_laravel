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
        Schema::create('ca_sharing_settings', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('ca_name')->default('');
            $table->string('ca_phone', 10)->default('');
            $table->string('ca_email')->nullable();
            $table->boolean('enabled')->default(false);
            $table->json('reports');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ca_sharing_settings');
    }
};
