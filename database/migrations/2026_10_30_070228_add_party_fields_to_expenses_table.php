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
        Schema::table('expenses', function (Blueprint $table) {
             $table->foreignId('party_id')
                ->nullable()
                ->after('user_id')
                ->constrained('parties')
                ->nullOnDelete();

            $table->string('party_name')
                ->nullable()
                ->after('party_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
             $table->dropForeign(['party_id']);
            $table->dropColumn(['party_id', 'party_name']);
        });
    }
};
