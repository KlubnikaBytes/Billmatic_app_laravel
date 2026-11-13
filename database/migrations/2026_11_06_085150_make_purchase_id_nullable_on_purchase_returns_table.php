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
        Schema::table('purchase_returns', function (Blueprint $table) {
             $table->dropForeign(['purchase_id']);
        });

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->nullable()->change();
        });

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->foreign('purchase_id')
                ->references('id')
                ->on('purchases')
                ->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         if (DB::table('purchase_returns')->whereNull('purchase_id')->exists()) {
            throw new RuntimeException(
                'Cannot roll back: unlinked purchase returns exist. Link or remove them first.'
            );
        }

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
        });

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->nullable(false)->change();
        });

        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->foreign('purchase_id')
                ->references('id')
                ->on('purchases')
                ->restrictOnDelete();
        });
    }
};
