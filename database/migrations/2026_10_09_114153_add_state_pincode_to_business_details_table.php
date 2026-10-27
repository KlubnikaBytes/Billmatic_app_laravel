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
    $table->string('business_state', 100)->nullable();
    $table->string('business_pincode', 6)->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('business_details', function (Blueprint $table) {
    $table->dropColumn(['business_state', 'business_pincode']);
});
    }
};
