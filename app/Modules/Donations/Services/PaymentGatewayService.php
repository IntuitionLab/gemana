<?php

namespace App\Modules\Donations\Services;

use App\Modules\Donations\Contracts\PaymentGatewayContract;
use App\Modules\Donations\Gateways\StripeGateway;
use App\Modules\Donations\Gateways\PayPalGateway;
use InvalidArgumentException;

/**
 * PaymentGatewayService
 *
 * Unified interface so the rest of the application never needs to know
 * which gateway is being used. Swap gateway by passing 'stripe' or 'paypal'.
 *
 * Usage:
 *   $gateway = app(PaymentGatewayService::class)->driver('stripe');
 *   $result  = $gateway->charge([...]);
 */
class PaymentGatewayService
{
    public function __construct(
        protected StripeGateway $stripe,
        protected PayPalGateway $paypal,
    ) {}

    /**
     * Resolve the requested gateway driver.
     */
    public function driver(string $gateway): PaymentGatewayContract
    {
        return match ($gateway) {
            'stripe' => $this->stripe,
            'paypal' => $this->paypal,
            default  => throw new InvalidArgumentException("Unsupported payment gateway: [{$gateway}]"),
        };
    }

    /**
     * Shorthand — charge via a specific gateway.
     */
    public function charge(string $gateway, array $payload): array
    {
        return $this->driver($gateway)->charge($payload);
    }

    /**
     * Shorthand — create a recurring subscription.
     */
    public function subscribe(string $gateway, array $payload): array
    {
        return $this->driver($gateway)->subscribe($payload);
    }

    /**
     * Shorthand — cancel a recurring subscription.
     */
    public function cancelSubscription(string $gateway, string $subscriptionId): bool
    {
        return $this->driver($gateway)->cancelSubscription($subscriptionId);
    }
}
