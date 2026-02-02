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
    Schema::table('items', function (Blueprint $table) {
        $table->boolean('low_stock_alert')->default(false);
        $table->decimal('low_stock_quantity', 10, 2)->nullable();
    });
}

public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn(['low_stock_alert', 'low_stock_quantity']);
    });
}

};
