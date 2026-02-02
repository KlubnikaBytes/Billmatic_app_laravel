<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_item_categories_to_items_table.php
public function up()
{
    Schema::table('items', function (Blueprint $table) {
        $table->json('item_categories')->nullable()->after('item_category_id');
    });
}

public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn('item_categories');
    });
}

};
