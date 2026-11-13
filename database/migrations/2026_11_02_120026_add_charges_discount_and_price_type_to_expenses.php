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
           $table->decimal('additional_charges', 12, 2)
                ->default(0)
                ->after('gst_amount');

            $table->decimal('discount', 12, 2)
                ->default(0)
                ->after('additional_charges');
        });

         Schema::table('expense_items', function (Blueprint $table) {
            $table->string('price_type', 20)
                ->default('Without Tax')
                ->after('rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
           $table->dropColumn('price_type');
        });

          Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'additional_charges',
                'discount',
            ]);
        });
    }
};
