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
        // database/migrations/xxxx_add_opening_balance_type_to_parties.php
    Schema::table('parties', function (Blueprint $table) {
    $table->enum('opening_balance_type', ['receive', 'pay'])
          ->default('receive')
          ->after('opening_balance');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            //
        });
    }
};
