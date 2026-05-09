<?php

namespace App\Modules\Donations\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Donations\Models\Donation;
use App\Modules\Donations\Services\DonationService;
use App\Modules\Donations\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donations,
        protected PaymentGatewayService $gateway,
    ) {}

    // ─── Public Form ──────────────────────────────────────────────────────────

    public function form()
    {
        return view('donations::form', [
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    // ─── Initiate Payment ─────────────────────────────────────────────────────

    public function initiate(Request $request)
    {
        $data = $request->validate([
            'amount'               => ['required', 'numeric', 'min:1'],
            'gateway'              => ['required', 'in:stripe,paypal'],
            'type'                 => ['required', 'in:one_off,recurring,in_memory'],
            'frequency'            => ['required_if:type,recurring', 'in:weekly,monthly,quarterly,annual'],
            'donor_name'           => ['required_unless:is_anonymous,true', 'nullable', 'string', 'max:100'],
            'donor_email'          => ['required_unless:is_anonymous,true', 'nullable', 'email'],
            'is_anonymous'         => ['boolean'],
            'tribute_name'         => ['nullable', 'string', 'max:100'],
            'tribute_type'         => ['nullable', 'in:in_memory,in_honour'],
            'tribute_notify_name'  => ['nullable', 'string', 'max:100'],
            'tribute_notify_email' => ['nullable', 'email'],
            'message'              => ['nullable', 'string', 'max:500'],
        ]);

        // Attach authenticated user if logged in
        $data['user_id'] = auth()->id();

        $result = $data['type'] === 'recurring'
            ? $this->donations->initiateRecurring($data)
            : $this->donations->initiateOneOff($data);

        if (! $result['success']) {
            return back()->withErrors(['payment' => $result['error']])->withInput();
        }

        // Stripe — return client_secret as JSON for Stripe Elements to handle
        if ($data['gateway'] === 'stripe') {
            return response()->json([
                'client_secret' => $result['client_secret'],
                'donation_id'   => $result['donation']->id ?? $result['plan']->id ?? null,
            ]);
        }

        // PayPal — redirect donor to PayPal approval page
        if ($data['gateway'] === 'paypal' && isset($result['approval_url'])) {
            return redirect($result['approval_url']);
        }

        return back()->withErrors(['payment' => 'Unable to initiate payment. Please try again.']);
    }

    // ─── PayPal Redirect Handlers ─────────────────────────────────────────────

    public function paypalReturn(Request $request)
    {
        $orderId = $request->query('token');

        if (! $orderId) {
            return redirect()->route('donations.form')->withErrors(['payment' => 'PayPal order not found.']);
        }

        // Capture the order
        $result = app(PaymentGatewayService::class)
            ->driver('paypal')
            ->captureOrder($orderId);

        if (! $result['success']) {
            return redirect()->route('donations.form')->withErrors(['payment' => 'PayPal payment could not be completed.']);
        }

        // Update donation status — webhook will also fire but we handle it here for immediacy
        Donation::where('gateway_reference', $orderId)->update([
            'status'         => 'completed',
            'gateway_status' => 'COMPLETED',
        ]);

        return redirect()->route('donations.thank-you');
    }

    public function paypalSubscriptionReturn(Request $request)
    {
        // PayPal subscription activation is confirmed via webhook
        // Just show the thank you page
        return redirect()->route('donations.thank-you');
    }

    public function paypalCancel()
    {
        return redirect()->route('donations.form')
            ->with('info', 'Your donation was cancelled. No payment was taken.');
    }

    // ─── Thank You ────────────────────────────────────────────────────────────

    public function thankYou()
    {
        return view('donations::thank-you');
    }
}
