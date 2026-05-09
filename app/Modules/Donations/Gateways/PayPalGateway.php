<?php

namespace App\Modules\Donations\Gateways;

use App\Modules\Donations\Contracts\PaymentGatewayContract;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PayPalGateway implements PaymentGatewayContract
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    // ─── One-off Charge ───────────────────────────────────────────────────────

    /**
     * Create a PayPal Order for a one-off donation.
     * Returns an approval_url the frontend redirects the donor to.
     */
    public function charge(array $payload): array
    {
        try {
            $response = $this->client()->post('/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => strtoupper($payload['currency'] ?? 'AUD'),
                            'value'         => number_format($payload['amount'], 2, '.', ''),
                        ],
                        'description' => $payload['description'] ?? 'Donation',
                        'custom_id'   => $payload['metadata']['donation_id'] ?? null,
                    ],
                ],
                'application_context' => [
                    'return_url' => route('donations.paypal.return'),
                    'cancel_url' => route('donations.paypal.cancel'),
                    'brand_name' => config('gemana.org_name', 'Gemana'),
                ],
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException($response->body());
            }

            $order       = $response->json();
            $approvalUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;

            return [
                'success'           => true,
                'gateway_reference' => $order['id'],
                'gateway_status'    => $order['status'],
                'approval_url'      => $approvalUrl,
            ];
        } catch (\Throwable $e) {
            Log::error('PayPal charge failed', ['error' => $e->getMessage(), 'payload' => $payload]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Capture an approved PayPal Order (called after donor returns from PayPal).
     */
    public function captureOrder(string $orderId): array
    {
        try {
            $response = $this->client()->post("/v2/checkout/orders/{$orderId}/capture");

            if (! $response->successful()) {
                throw new \RuntimeException($response->body());
            }

            $order = $response->json();

            return [
                'success'           => true,
                'gateway_reference' => $order['id'],
                'gateway_status'    => $order['status'],
            ];
        } catch (\Throwable $e) {
            Log::error('PayPal capture failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    // ─── Recurring Subscription ───────────────────────────────────────────────

    /**
     * Create a PayPal Subscription for recurring donations.
     *
     * Flow:
     *  1. Create a Plan (product + billing cycle)
     *  2. Create a Subscription against that plan
     *  3. Return approval_url for frontend redirect
     */
    public function subscribe(array $payload): array
    {
        try {
            // 1. Create a product
            $productResponse = $this->client()->post('/v1/catalogs/products', [
                'name'        => 'Recurring Donation',
                'description' => 'Recurring donation — ' . ucfirst($payload['frequency']),
                'type'        => 'SERVICE',
            ]);

            $product = $productResponse->json();

            // 2. Create a plan
            $planResponse = $this->client()->post('/v1/billing/plans', [
                'product_id'          => $product['id'],
                'name'                => 'Donation Plan — ' . ucfirst($payload['frequency']),
                'status'              => 'ACTIVE',
                'billing_cycles'      => [
                    [
                        'frequency' => [
                            'interval_unit'  => $this->paypalInterval($payload['frequency']),
                            'interval_count' => $this->paypalIntervalCount($payload['frequency']),
                        ],
                        'tenure_type'  => 'REGULAR',
                        'sequence'     => 1,
                        'total_cycles' => 0, // 0 = infinite
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'value'         => number_format($payload['amount'], 2, '.', ''),
                                'currency_code' => strtoupper($payload['currency'] ?? 'AUD'),
                            ],
                        ],
                    ],
                ],
                'payment_preferences' => [
                    'auto_bill_outstanding'     => true,
                    'setup_fee_failure_action'  => 'CONTINUE',
                    'payment_failure_threshold' => 3,
                ],
            ]);

            $plan = $planResponse->json();

            // 3. Create subscription
            $subResponse = $this->client()->post('/v1/billing/subscriptions', [
                'plan_id'    => $plan['id'],
                'subscriber' => [
                    'name'          => ['given_name' => $payload['donor_name'] ?? ''],
                    'email_address' => $payload['donor_email'] ?? '',
                ],
                'application_context' => [
                    'return_url' => route('donations.paypal.subscription.return'),
                    'cancel_url' => route('donations.paypal.cancel'),
                    'brand_name' => config('gemana.org_name', 'Gemana'),
                ],
            ]);

            $subscription = $subResponse->json();
            $approvalUrl  = collect($subscription['links'])->firstWhere('rel', 'approve')['href'] ?? null;

            return [
                'success'                 => true,
                'gateway_subscription_id' => $subscription['id'],
                'gateway_plan_id'         => $plan['id'],
                'gateway_status'          => $subscription['status'],
                'approval_url'            => $approvalUrl,
            ];
        } catch (\Throwable $e) {
            Log::error('PayPal subscription failed', ['error' => $e->getMessage(), 'payload' => $payload]);

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
            $response = $this->client()->post("/v1/billing/subscriptions/{$subscriptionId}/cancel", [
                'reason' => 'Cancelled by donor or administrator.',
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('PayPal cancel subscription failed', [
                'subscription_id' => $subscriptionId,
                'error'           => $e->getMessage(),
            ]);

            return false;
        }
    }

    // ─── Webhook ──────────────────────────────────────────────────────────────

    /**
     * Verify PayPal webhook and return a normalised event.
     * PayPal uses an API-based verification (not HMAC signature like Stripe).
     */
    public function parseWebhook(array $payload): array
    {
        // Verify authenticity via PayPal's verify-webhook-signature endpoint
        $verified = $this->client()->post('/v1/notifications/verify-webhook-signature', [
            'auth_algo'         => $payload['auth_algo'],
            'cert_url'          => $payload['cert_url'],
            'transmission_id'   => $payload['transmission_id'],
            'transmission_sig'  => $payload['transmission_sig'],
            'transmission_time' => $payload['transmission_time'],
            'webhook_id'        => config('services.paypal.webhook_id'),
            'webhook_event'     => $payload['raw_body'],
        ])->json();

        if (($verified['verification_status'] ?? '') !== 'SUCCESS') {
            Log::warning('PayPal webhook verification failed', $verified);
            throw new \RuntimeException('PayPal webhook signature verification failed.');
        }

        $event     = $payload['raw_body'];
        $eventType = $event['event_type'] ?? '';
        $resource  = $event['resource'] ?? [];

        return match (true) {
            str_contains($eventType, 'PAYMENT.CAPTURE.COMPLETED') => [
                'event_type' => 'payment.completed',
                'reference'  => $resource['id'] ?? null,
                'amount'     => (int) round(($resource['amount']['value'] ?? 0) * 100),
                'currency'   => strtoupper($resource['amount']['currency_code'] ?? 'AUD'),
                'metadata'   => [],
                'raw'        => $event,
            ],
            str_contains($eventType, 'PAYMENT.CAPTURE.DENIED') => [
                'event_type' => 'payment.failed',
                'reference'  => $resource['id'] ?? null,
                'amount'     => 0,
                'currency'   => 'AUD',
                'metadata'   => [],
                'raw'        => $event,
            ],
            str_contains($eventType, 'BILLING.SUBSCRIPTION.CANCELLED') => [
                'event_type' => 'subscription.cancelled',
                'reference'  => $resource['id'] ?? null,
                'amount'     => 0,
                'currency'   => 'AUD',
                'metadata'   => [],
                'raw'        => $event,
            ],
            str_contains($eventType, 'PAYMENT.SALE.COMPLETED') => [
                'event_type' => 'subscription.payment_completed',
                'reference'  => $resource['billing_agreement_id'] ?? $resource['id'] ?? null,
                'amount'     => (int) round(($resource['amount']['total'] ?? 0) * 100),
                'currency'   => strtoupper($resource['amount']['currency'] ?? 'AUD'),
                'metadata'   => [],
                'raw'        => $event,
            ],
            default => [
                'event_type' => $eventType,
                'reference'  => null,
                'amount'     => 0,
                'currency'   => 'AUD',
                'metadata'   => [],
                'raw'        => $event,
            ],
        };
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Build an authenticated HTTP client using a cached OAuth2 access token.
     */
    private function client(): PendingRequest
    {
        return Http::withToken($this->accessToken())
                   ->baseUrl($this->baseUrl)
                   ->acceptJson()
                   ->asJson();
    }

    /**
     * Fetch (or retrieve from cache) a PayPal OAuth2 access token.
     * Tokens are valid for ~9 hours; we cache for 8 to be safe.
     */
    private function accessToken(): string
    {
        return Cache::remember('paypal_access_token', now()->addHours(8), function () {
            $response = Http::baseUrl($this->baseUrl)
                ->withBasicAuth(
                    config('services.paypal.client_id'),
                    config('services.paypal.client_secret'),
                )
                ->asForm()
                ->post('/v1/oauth2/token', ['grant_type' => 'client_credentials']);

            return $response->json('access_token');
        });
    }

    private function paypalInterval(string $frequency): string
    {
        return match ($frequency) {
            'weekly'    => 'WEEK',
            'monthly'   => 'MONTH',
            'quarterly' => 'MONTH',
            'annual'    => 'YEAR',
            default     => 'MONTH',
        };
    }

    private function paypalIntervalCount(string $frequency): int
    {
        return $frequency === 'quarterly' ? 3 : 1;
    }
}
