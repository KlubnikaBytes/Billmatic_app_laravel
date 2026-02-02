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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('opening_stock', 12, 2)
                  ->after('item_id');

            $table->decimal('closing_stock', 12, 2)
                  ->after('opening_stock');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['opening_stock', 'closing_stock']);
        });
    }
};
