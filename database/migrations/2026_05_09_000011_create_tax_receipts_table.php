<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_receipts', function (Blueprint $table) {
            $table->id();

            // Linked donation
            $table->foreignId('donation_id')
                  ->constrained('donations')
                  ->cascadeOnDelete();

            // Linked donor (nullable — anonymous donations have no user)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Receipt identity
            // Format: GEM-2025-00001 (prefix + financial year + sequential number)
            $table->string('receipt_number')->unique();
            $table->string('financial_year');   // e.g. "2024-25" (Australian FY: July–June)

            // Snapshot of org details at time of issue
            // (org settings may change — receipt must reflect what was current)
            $table->string('org_name');
            $table->string('org_abn')->nullable();
            $table->boolean('org_is_dgr')->default(false);
            $table->string('org_address')->nullable();

            // Snapshot of donor details at time of issue
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donor_address')->nullable();

            // Amount
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AUD');

            // PDF storage
            // Path relative to storage/app/private/receipts/
            $table->string('pdf_path')->nullable();

            // Delivery
            $table->timestamp('emailed_at')->nullable();
            $table->string('emailed_to')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_receipts');
    }
};
