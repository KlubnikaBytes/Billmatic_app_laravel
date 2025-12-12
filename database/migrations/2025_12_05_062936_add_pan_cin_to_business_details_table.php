<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('business_details', function (Blueprint $table) {
        $table->string('pan_cin')->nullable();
    });
}

public function down()
{
    Schema::table('business_details', function (Blueprint $table) {
        $table->dropColumn('pan_cin');
    });
}

};
