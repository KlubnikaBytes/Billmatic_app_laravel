<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_show_in_online_store_to_items_table.php
public function up()
{
    Schema::table('items', function (Blueprint $table) {
        $table->boolean('show_in_online_store')->default(false)->after('description');
    });
}

public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn('show_in_online_store');
    });
}

};
