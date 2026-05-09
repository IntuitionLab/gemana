<?php

namespace App\Modules\Donations\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Donations\Models\Donation;
use App\Modules\Donations\Models\DonationPlan;
use App\Modules\Donations\Services\DonationService;
use App\Modules\Donations\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $gateway,
        protected DonationService $donations,
    ) {}

    // ─── Stripe Webhook ───────────────────────────────────────────────────────

    public function stripe(Request $request): Response
    {
        try {
            $event = $this->gateway->driver('stripe')->parseWebhook([
                'raw_body'  => $request->getContent(),
                'signature' => $request->header('Stripe-Signature'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook rejected', ['error' => $e->getMessage()]);
            return response('Webhook signature verification failed.', 400);
        }

        $this->handleEvent($event);

        return response('OK', 200);
    }

    // ─── PayPal Webhook ───────────────────────────────────────────────────────

    public function paypal(Request $request): Response
    {
        try {
            $event = $this->gateway->driver('paypal')->parseWebhook([
                'raw_body'          => $request->all(),
                'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url'          => $request->header('PAYPAL-CERT-URL'),
                'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('PayPal webhook rejected', ['error' => $e->getMessage()]);
            return response('Webhook verification failed.', 400);
        }

        $this->handleEvent($event);

        return response('OK', 200);
    }

    // ─── Event Handler ────────────────────────────────────────────────────────

    /**
     * Route normalised gateway events to the appropriate handler.
     */
    private function handleEvent(array $event): void
    {
        match ($event['event_type']) {
            'payment.completed'              => $this->onPaymentCompleted($event),
            'payment.failed'                 => $this->onPaymentFailed($event),
            'subscription.cancelled'         => $this->onSubscriptionCancelled($event),
            'subscription.payment_completed' => $this->onSubscriptionPaymentCompleted($event),
            default                          => Log::info('Unhandled gateway event', ['type' => $event['event_type']]),
        };
    }

    // ─── Individual Event Handlers ────────────────────────────────────────────

    private function onPaymentCompleted(array $event): void
    {
        $donation = Donation::where('gateway_reference', $event['reference'])->first();

        if (! $donation) {
            Log::warning('Webhook: donation not found for reference', ['ref' => $event['reference']]);
            return;
        }

        $donation->update([
            'status'         => 'completed',
            'gateway_status' => $event['gateway_status'] ?? 'succeeded',
        ]);

        // Issue tax receipt
        $this->donations->issueReceipt($donation);
    }

    private function onPaymentFailed(array $event): void
    {
        Donation::where('gateway_reference', $event['reference'])
            ->update([
                'status'         => 'failed',
                'gateway_status' => 'failed',
            ]);
    }

    private function onSubscriptionCancelled(array $event): void
    {
        DonationPlan::where('gateway_subscription_id', $event['reference'])
            ->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);
    }

    private function onSubscriptionPaymentCompleted(array $event): void
    {
        // Find the active plan
        $plan = DonationPlan::where('gateway_subscription_id', $event['reference'])->first();

        if (! $plan) {
            Log::warning('Webhook: plan not found for subscription', ['ref' => $event['reference']]);
            return;
        }

        // Create a new donation record for this billing cycle
        $donation = Donation::create([
            'user_id'          => $plan->user_id,
            'donor_name'       => $plan->donor_name,
            'donor_email'      => $plan->donor_email,
            'amount'           => $plan->amount,
            'currency'         => $plan->currency,
            'type'             => 'recurring',
            'status'           => 'completed',
            'gateway'          => $plan->gateway,
            'gateway_status'   => 'succeeded',
            'donation_plan_id' => $plan->id,
        ]);

        // Update next billing date
        $plan->update([
            'next_billing_date' => $this->nextBillingDate($plan->frequency),
        ]);

        // Issue tax receipt for this cycle
        $this->donations->issueReceipt($donation);
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
