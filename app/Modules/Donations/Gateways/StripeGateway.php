<?php

namespace App\Modules\Donations\Gateways;

use App\Modules\Donations\Contracts\PaymentGatewayContract;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Customer;
use Stripe\Webhook;

class StripeGateway implements PaymentGatewayContract
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ─── One-off Charge ───────────────────────────────────────────────────────

    /**
     * Create a Stripe PaymentIntent for a one-off donation.
     * Returns a client_secret the frontend uses to complete payment with Stripe Elements.
     */
    public function charge(array $payload): array
    {
        try {
            $intent = PaymentIntent::create([
                'amount'      => $this->toCents($payload['amount']),
                'currency'    => strtolower($payload['currency'] ?? 'aud'),
                'description' => $payload['description'] ?? 'Donation',
                'metadata'    => $payload['metadata'] ?? [],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return [
                'success'           => true,
                'gateway_reference' => $intent->id,
                'gateway_status'    => $intent->status,
                'client_secret'     => $intent->client_secret,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe charge failed', ['error' => $e->getMessage(), 'payload' => $payload]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    // ─── Recurring Subscription ───────────────────────────────────────────────

    /**
     * Create a Stripe Subscription for recurring donations.
     *
     * Flow:
     *  1. Create (or retrieve) a Stripe Customer for the donor
     *  2. Create a Product + Price for the donation amount/frequency
     *  3. Create a Subscription with payment_behavior = 'default_incomplete'
     *     so the first payment still requires client-side confirmation
     */
    public function subscribe(array $payload): array
    {
        try {
            // 1. Create customer
            $customer = Customer::create([
                'email' => $payload['donor_email'],
                'name'  => $payload['donor_name'] ?? null,
                'metadata' => $payload['metadata'] ?? [],
            ]);

            // 2. Create a Product for this org (idempotent-ish — in production you'd
            //    cache/reuse products per frequency tier)
            $product = Product::create([
                'name' => 'Recurring Donation — ' . ucfirst($payload['frequency']),
            ]);

            // 3. Create a Price
            $price = Price::create([
                'product'     => $product->id,
                'unit_amount' => $this->toCents($payload['amount']),
                'currency'    => strtolower($payload['currency'] ?? 'aud'),
                'recurring'   => [
                    'interval'       => $this->stripeInterval($payload['frequency']),
                    'interval_count' => $this->stripeIntervalCount($payload['frequency']),
                ],
            ]);

            // 4. Create Subscription
            $subscription = Subscription::create([
                'customer'         => $customer->id,
                'items'            => [['price' => $price->id]],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                'expand'           => ['latest_invoice.payment_intent'],
                'metadata'         => $payload['metadata'] ?? [],
            ]);

            $clientSecret = $subscription->latest_invoice->payment_intent->client_secret;

            return [
                'success'                  => true,
                'gateway_subscription_id'  => $subscription->id,
                'gateway_plan_id'          => $price->id,
                'gateway_status'           => $subscription->status,
                'client_secret'            => $clientSecret,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription failed', ['error' => $e->getMessage(), 'payload' => $payload]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    // ─── Cancel Subscription ──────────────────────────────────────────────────

    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);
            $subscription->cancel();

            return true;
        } catch (ApiErrorException $e) {
            Log::error('Stripe cancel subscription failed', [
                'subscription_id' => $subscriptionId,
                'error'           => $e->getMessage(),
            ]);

            return false;
        }
    }

    // ─── Webhook ──────────────────────────────────────────────────────────────

    /**
     * Verify the Stripe webhook signature and return a normalised event.
     */
    public function parseWebhook(array $payload): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload['raw_body'],
                $payload['signature'],
                config('services.stripe.webhook_secret'),
            );

            return match ($event->type) {
                'payment_intent.succeeded' => [
                    'event_type' => 'payment.completed',
                    'reference'  => $event->data->object->id,
                    'amount'     => $event->data->object->amount,
                    'currency'   => strtoupper($event->data->object->currency),
                    'metadata'   => (array) $event->data->object->metadata,
                    'raw'        => $event->toArray(),
                ],
                'payment_intent.payment_failed' => [
                    'event_type' => 'payment.failed',
                    'reference'  => $event->data->object->id,
                    'amount'     => $event->data->object->amount,
                    'currency'   => strtoupper($event->data->object->currency),
                    'metadata'   => (array) $event->data->object->metadata,
                    'raw'        => $event->toArray(),
                ],
                'customer.subscription.deleted' => [
                    'event_type' => 'subscription.cancelled',
                    'reference'  => $event->data->object->id,
                    'amount'     => 0,
                    'currency'   => 'AUD',
                    'metadata'   => (array) $event->data->object->metadata,
                    'raw'        => $event->toArray(),
                ],
                'invoice.payment_succeeded' => [
                    'event_type' => 'subscription.payment_completed',
                    'reference'  => $event->data->object->subscription,
                    'amount'     => $event->data->object->amount_paid,
                    'currency'   => strtoupper($event->data->object->currency),
                    'metadata'   => (array) $event->data->object->metadata,
                    'raw'        => $event->toArray(),
                ],
                default => [
                    'event_type' => $event->type,
                    'reference'  => null,
                    'amount'     => 0,
                    'currency'   => 'AUD',
                    'metadata'   => [],
                    'raw'        => $event->toArray(),
                ],
            };
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Convert dollars to cents (Stripe works in smallest currency unit).
     */
    private function toCents(float|int $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Map Gemana frequency to Stripe interval.
     */
    private function stripeInterval(string $frequency): string
    {
        return match ($frequency) {
            'weekly'    => 'week',
            'monthly'   => 'month',
            'quarterly' => 'month',
            'annual'    => 'year',
            default     => 'month',
        };
    }

    /**
     * Quarterly = every 3 months in Stripe.
     */
    private function stripeIntervalCount(string $frequency): int
    {
        return $frequency === 'quarterly' ? 3 : 1;
    }
}
