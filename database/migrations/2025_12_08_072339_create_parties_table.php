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
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // owner (your logged-in user)

            $table->string('party_name');
            $table->string('contact_number')->nullable();
            $table->enum('party_type', ['customer', 'supplier'])->default('customer');

            // Business Info
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();

            // Billing Address
            $table->string('billing_street')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_pincode', 10)->nullable();
            $table->string('billing_city')->nullable();

            // Credit Info
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->integer('credit_period_days')->default(0);
            $table->decimal('credit_limit', 15, 2)->default(0);

            // Other Details
            $table->unsignedBigInteger('party_category_id')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->date('dob')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('party_category_id')->references('id')->on('party_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
