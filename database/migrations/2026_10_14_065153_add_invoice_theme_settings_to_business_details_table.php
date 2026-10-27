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
            $table->string('invoice_theme', 100)->default('None');
            $table->string('invoice_template', 50)->default('luxury');
            $table->string('invoice_theme_color', 7)->default('#C62828');
        });
    }

    public function down(): void
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_theme',
                'invoice_template',
                'invoice_theme_color',
            ]);
        });
    }
};
