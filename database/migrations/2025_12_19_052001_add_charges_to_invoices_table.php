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
    Schema::table('invoices', function (Blueprint $table) {
        $table->decimal('additional_charges', 10, 2)->default(0);
        $table->decimal('discount_amount', 10, 2)->default(0);
        $table->decimal('round_off', 10, 2)->default(0);
        $table->decimal('tcs_amount', 10, 2)->default(0);
    });
}

public function down()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn([
            'additional_charges',
            'discount_amount',
            'round_off',
            'tcs_amount',
        ]);
    });
}

};
