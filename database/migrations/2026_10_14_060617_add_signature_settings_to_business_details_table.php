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
        $table->longText('signature_image')->nullable();
        $table->boolean('show_empty_signature_box')->default(false);
        $table->boolean('enable_receiver_signature')->default(false);
    });
}

public function down(): void
{
    Schema::table('business_details', function (Blueprint $table) {
        $table->dropColumn([
            'signature_image',
            'show_empty_signature_box',
            'enable_receiver_signature',
        ]);
    });
}

};
