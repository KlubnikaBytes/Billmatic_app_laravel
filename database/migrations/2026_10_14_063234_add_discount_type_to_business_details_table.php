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
        Schema::table('business_details', function (Blueprint $table) {
            $table->string('discount_type', 20)
                ->default('after_tax');
        });
    }

    public function down(): void
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->dropColumn('discount_type');
        });
    }
};
