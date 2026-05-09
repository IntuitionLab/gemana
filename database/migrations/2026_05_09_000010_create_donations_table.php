<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            // Donor — nullable for anonymous donations
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Donor details captured at time of donation
            // (stored separately so they persist even if user is deleted)
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->boolean('is_anonymous')->default(false);

            // Amount & currency
            // Currency defaults to AUD — can be changed via Organisation Settings later
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AUD');

            // Donation type
            $table->enum('type', ['one_off', 'recurring', 'in_memory'])->default('one_off');

            // Status
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'cancelled'])
                  ->default('pending');

            // Payment gateway
            $table->enum('gateway', ['stripe', 'paypal', 'manual'])->nullable();
            $table->string('gateway_reference')->nullable(); // Stripe PaymentIntent ID / PayPal order ID
            $table->string('gateway_status')->nullable();    // Raw status from gateway

            // Recurring plan link (null for one-off)
            $table->foreignId('donation_plan_id')
                  ->nullable()
                  ->constrained('donation_plans')
                  ->nullOnDelete();

            // In-memory / tribute details
            $table->string('tribute_name')->nullable();       // "In memory of John Smith"
            $table->string('tribute_type')->nullable();       // 'in_memory' | 'in_honour'
            $table->string('tribute_notify_name')->nullable();  // Notify this person
            $table->string('tribute_notify_email')->nullable();

            // Donor message
            $table->text('message')->nullable();

            // Tax receipt
            $table->boolean('tax_receipt_issued')->default(false);
            $table->timestamp('tax_receipt_issued_at')->nullable();

            // Soft deletes + timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
