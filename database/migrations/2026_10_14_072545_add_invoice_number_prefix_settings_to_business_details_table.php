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
            $table->string('invoice_number_prefix', 20)->default('INV-');
            $table->unsignedBigInteger('starting_serial_number')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number_prefix',
                'starting_serial_number',
            ]);
        });
    }
};
