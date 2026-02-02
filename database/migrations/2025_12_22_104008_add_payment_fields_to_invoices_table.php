<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_payment_fields_to_invoices_table.php
public function up()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->decimal('received_amount', 10, 2)->default(0);
        $table->decimal('balance_amount', 10, 2)->default(0);
        $table->string('payment_mode')->nullable();
    });
}

public function down()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn([
            'received_amount',
            'balance_amount',
            'payment_mode',
        ]);
    });
}

};
