<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_plans', function (Blueprint $table) {
            $table->id();

            // Owner
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Plan details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AUD');
            $table->enum('frequency', ['weekly', 'monthly', 'quarterly', 'annual']);

            // Status
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired'])
                  ->default('active');

            // Gateway
            $table->enum('gateway', ['stripe', 'paypal']);
            $table->string('gateway_subscription_id')->nullable(); // Stripe sub ID / PayPal sub ID
            $table->string('gateway_plan_id')->nullable();         // Stripe price ID / PayPal plan ID

            // Billing schedule
            $table->date('started_at')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->date('cancelled_at')->nullable();

            // Donor details captured at signup
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_plans');
    }
};
