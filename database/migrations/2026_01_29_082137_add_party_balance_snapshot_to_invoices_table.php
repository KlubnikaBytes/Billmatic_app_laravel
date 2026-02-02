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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('party_opening_balance', 12, 2)
                  ->after('party_id');

            $table->decimal('party_closing_balance', 12, 2)
                  ->after('balance_amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'party_opening_balance',
                'party_closing_balance'
            ]);
        });
    }
};
