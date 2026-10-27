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
        $table->string('terms_invoice_type')
            ->default('Sales Invoice');

        $table->text('terms_and_conditions')->nullable();
    });
}

public function down(): void
{
    Schema::table('business_details', function (Blueprint $table) {
        $table->dropColumn([
            'terms_invoice_type',
            'terms_and_conditions',
        ]);
    });
}
};
