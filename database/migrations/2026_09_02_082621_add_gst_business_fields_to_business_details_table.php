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

            $table->string('business_name')->nullable();
            $table->text('business_address')->nullable();

            $table->string('gst_status')->nullable();
            $table->string('gst_legal_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('business_details', function (Blueprint $table) {

            $table->dropColumn([
                'business_name',
                'business_address',
                'gst_status',
                'gst_legal_name',
            ]);
        });
    }
};
