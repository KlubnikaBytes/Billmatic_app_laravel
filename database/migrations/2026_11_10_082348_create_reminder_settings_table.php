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
        Schema::create('reminder_settings', function (Blueprint $table) {
            $table->id();
        $table->foreignId('user_id')->unique()
            ->constrained()->cascadeOnDelete();

        $table->boolean('sms_on_transaction')->default(true);
        $table->boolean('payment_reminder_due')->default(true);
        $table->boolean('payment_reminder')->default(true);
        $table->boolean('daily_outstanding')->default(true);
        $table->boolean('daily_sales')->default(true);
        $table->boolean('low_stock')->default(true);
        $table->boolean('service_reminder')->default(false);
        $table->boolean('whatsapp_alert')->default(false);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_settings');
    }
};
