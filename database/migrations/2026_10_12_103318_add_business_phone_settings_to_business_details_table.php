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
        $table->string('business_phone_number', 20)
            ->nullable();

        $table->boolean('show_phone_on_invoice')
            ->default(true);
    });
}

public function down(): void
{
    Schema::table('business_details', function (Blueprint $table) {
        $table->dropColumn([
            'business_phone_number',
            'show_phone_on_invoice',
        ]);
    });
}

};
