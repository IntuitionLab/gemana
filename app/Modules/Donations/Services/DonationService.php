<?php

namespace App\Modules\Donations\Services;

use App\Modules\Donations\Models\Donation;
use App\Modules\Donations\Models\DonationPlan;
use App\Modules\Donations\Models\TaxReceipt;
use App\Modules\Donations\Jobs\SendTaxReceiptJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DonationService
{
    public function __construct(
        protected PaymentGatewayService $gateway,
    ) {}

    // ─── One-off Donation ─────────────────────────────────────────────────────

    /**
     * Initiate a one-off donation.
     *
     * Creates a pending Donation record, then calls the gateway to create
     * a PaymentIntent (Stripe) or Order (PayPal). The frontend uses the
     * returned client_secret / approval_url to complete payment.
     *
     * The donation is only marked 'completed' when the webhook fires.
     */
    public function initiateOneOff(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $donation = Donation::create([
                'user_id'      => $data['user_id'] ?? null,
                'donor_name'   => $data['is_anonymous'] ? null : ($data['donor_name'] ?? null),
                'donor_email'  => $data['is_anonymous'] ? null : ($data['donor_email'] ?? null),
                'is_anonymous' => $data['is_anonymous'] ?? false,
                'amount'       => $data['amount'],
                'currency'     => $data['currency'] ?? 'AUD',
                'type'         => 'one_off',
                'status'       => 'pending',
                'gateway'      => $data['gateway'],
                // Tribute fields
                'tribute_name'          => $data['tribute_name'] ?? null,
                'tribute_type'          => $data['tribute_type'] ?? null,
                'tribute_notify_name'   => $data['tribute_notify_name'] ?? null,
                'tribute_notify_email'  => $data['tribute_notify_email'] ?? null,
                'message'               => $data['message'] ?? null,
            ]);

            // Call the gateway
            $result = $this->gateway->charge($data['gateway'], [
                'amount'      => $data['amount'],
                'currency'    => $data['currency'] ?? 'AUD',
                'description' => 'Donation — ' . config('gemana.org_name', 'Gemana'),
                'metadata'    => [
                    'donation_id' => $donation->id,
                    'donor_name'  => $donation->displayName(),
                ],
            ]);

            if (! $result['success']) {
                $donation->update(['status' => 'failed']);

                return [
                    'success' => false,
                    'error'   => $result['error'],
                ];
            }

            // Store the gateway reference on the donation
            $donation->update([
                'gateway_reference' => $result['gateway_reference'],
                'gateway_status'    => $result['gateway_status'],
            ]);

            return [
                'success'      => true,
                'donation'     => $donation,
                // Stripe: client_secret for Stripe Elements
                // PayPal: approval_url for redirect
                'client_secret'  => $result['client_secret'] ?? null,
                'approval_url'   => $result['approval_url'] ?? null,
            ];
        });
    }

    // ─── Recurring Donation ───────────────────────────────────────────────────

    /**
     * Initiate a recurring donation subscription.
     *
     * Creates a DonationPlan record, then calls the gateway to set up
     * the subscription. A Donation record is created for each billing
     * cycle by the webhook handler.
     */
    public function initiateRecurring(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $plan = DonationPlan::create([
                'user_id'     => $data['user_id'] ?? null,
                'amount'      => $data['amount'],
                'currency'    => $data['currency'] ?? 'AUD',
                'frequency'   => $data['frequency'],
                'status'      => 'active',
                'gateway'     => $data['gateway'],
                'donor_name'  => $data['donor_name'] ?? null,
                'donor_email' => $data['donor_email'] ?? null,
                'started_at'  => now(),
                'next_billing_date' => $this->nextBillingDate($data['frequency']),
            ]);

            $result = $this->gateway->subscribe($data['gateway'], [
                'amount'       => $data['amount'],
                'currency'     => $data['currency'] ?? 'AUD',
                'frequency'    => $data['frequency'],
                'donor_name'   => $data['donor_name'] ?? null,
                'donor_email'  => $data['donor_email'] ?? null,
                'metadata'     => [
                    'plan_id'    => $plan->id,
                    'donor_name' => $data['donor_name'] ?? null,
                ],
            ]);

            if (! $result['success']) {
                $plan->update(['status' => 'cancelled']);

                return [
                    'success' => false,
                    'error'   => $result['error'],
                ];
            }

            $plan->update([
                'gateway_subscription_id' => $result['gateway_subscription_id'],
                'gateway_plan_id'         => $result['gateway_plan_id'],
            ]);

            return [
                'success'      => true,
                'plan'         => $plan,
                'client_secret'  => $result['client_secret'] ?? null,
                'approval_url'   => $result['approval_url'] ?? null,
            ];
        });
    }

    // ─── Tax Receipt ──────────────────────────────────────────────────────────

    /**
     * Issue a tax receipt for a completed donation.
     *
     * - Generates a sequential receipt number
     * - Snapshots org + donor details at the time of issue
     * - Dispatches a queued job to generate the PDF and email it
     */
    public function issueReceipt(Donation $donation): ?TaxReceipt
    {
        // Don't double-issue
        if ($donation->tax_receipt_issued) {
            return $donation->receipt;
        }

        // Anonymous donations don't get receipts
        if ($donation->is_anonymous) {
            return null;
        }

        $financialYear = $donation->financialYear();

        $receipt = TaxReceipt::create([
            'donation_id'    => $donation->id,
            'user_id'        => $donation->user_id,
            'receipt_number' => TaxReceipt::nextReceiptNumber($financialYear),
            'financial_year' => $financialYear,

            // Org snapshot — reads from config; will read from DB once
            // Organisation Settings is built (see backlog)
            'org_name'       => config('gemana.org_name', 'Gemana'),
            'org_abn'        => config('gemana.abn'),
            'org_is_dgr'     => config('gemana.is_dgr', false),
            'org_address'    => config('gemana.address'),

            // Donor snapshot
            'donor_name'     => $donation->displayName(),
            'donor_email'    => $donation->donor_email ?? $donation->user?->email,
            'donor_address'  => null, // Extended in future when member profiles have addresses

            'amount'         => $donation->amount,
            'currency'       => $donation->currency,
        ]);

        // Mark donation as receipted
        $donation->update([
            'tax_receipt_issued'    => true,
            'tax_receipt_issued_at' => now(),
        ]);

        // Dispatch background job to generate PDF + send email
        SendTaxReceiptJob::dispatch($receipt);

        return $receipt;
    }

    // ─── Cancel Recurring Plan ────────────────────────────────────────────────

    public function cancelPlan(DonationPlan $plan): bool
    {
        $cancelled = $this->gateway->cancelSubscription($plan->gateway, $plan->gateway_subscription_id);

        if ($cancelled) {
            $plan->cancel();
        }

        return $cancelled;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function nextBillingDate(string $frequency): \Carbon\Carbon
    {
        return match ($frequency) {
            'weekly'    => now()->addWeek(),
            'monthly'   => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'annual'    => now()->addYear(),
            default     => now()->addMonth(),
        };
    }
}
