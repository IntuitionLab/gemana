<?php

namespace App\Modules\Donations\Contracts;

interface PaymentGatewayContract
{
    /**
     * Charge a one-off payment.
     *
     * Expected payload keys:
     *   - amount        int      Amount in cents (e.g. 5000 = $50.00)
     *   - currency      string   ISO currency code (e.g. 'AUD')
     *   - description   string   Human-readable donation description
     *   - metadata      array    Optional key/value pairs stored on the gateway
     *
     * Returns array with at minimum:
     *   - success           bool
     *   - gateway_reference string   Gateway's transaction/order ID
     *   - gateway_status    string   Raw status from gateway
     *   - client_secret     string   (Stripe) or approval_url string (PayPal) for frontend
     *   - error             string   Present only on failure
     */
    public function charge(array $payload): array;

    /**
     * Create a recurring subscription.
     *
     * Expected payload keys:
     *   - amount            int      Amount in cents per billing cycle
     *   - currency          string   ISO currency code
     *   - frequency         string   'weekly' | 'monthly' | 'quarterly' | 'annual'
     *   - donor_email       string
     *   - donor_name        string
     *   - metadata          array    Optional
     *
     * Returns array with at minimum:
     *   - success               bool
     *   - gateway_subscription_id string
     *   - gateway_plan_id         string
     *   - gateway_status          string
     *   - client_secret           string   (Stripe) or approval_url (PayPal)
     *   - error                   string   Present only on failure
     */
    public function subscribe(array $payload): array;

    /**
     * Cancel a recurring subscription by its gateway ID.
     *
     * Returns true on success, false on failure.
     */
    public function cancelSubscription(string $subscriptionId): bool;

    /**
     * Verify and parse an inbound webhook payload.
     *
     * Expected payload keys:
     *   - raw_body     string   Raw request body (needed for signature verification)
     *   - signature    string   Gateway-specific signature header value
     *
     * Returns normalised event array:
     *   - event_type   string   e.g. 'payment.completed', 'subscription.cancelled'
     *   - reference    string   Gateway transaction / subscription ID
     *   - amount       int      Amount in cents
     *   - currency     string
     *   - metadata     array
     *   - raw          array    Full parsed gateway payload
     */
    public function parseWebhook(array $payload): array;
}
