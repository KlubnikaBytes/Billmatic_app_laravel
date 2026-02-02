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
        $table->decimal('stock_value', 15, 2)->default(0)->after('opening_stock');
    });
}

public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn('stock_value');
    });
}

};
