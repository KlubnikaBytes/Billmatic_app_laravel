<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Convert existing string values into JSON array ["value"]
        DB::table('business_details')->get()->each(function ($row) {
            if (!empty($row->business_type) && $row->business_type[0] !== '[') {
                DB::table('business_details')
                    ->where('id', $row->id)
                    ->update([
                        'business_type' => json_encode([$row->business_type])
                    ]);
            }
        });

        // 2. Now safely change column type to JSON
        Schema::table('business_details', function (Blueprint $table) {
            $table->json('business_type')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('business_details', function (Blueprint $table) {
            $table->string('business_type')->change();
        });
    }
};
