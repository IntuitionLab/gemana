<?php

use App\Modules\Donations\Http\Controllers\DonationController;
use App\Modules\Donations\Http\Controllers\WebhookController;
use App\Modules\Donations\Http\Controllers\Admin\DonationsAdminController;
use App\Modules\Donations\Http\Controllers\Portal\DonationPortalController;
use Illuminate\Support\Facades\Route;

// ─── Webhooks (no CSRF, no auth) ──────────────────────────────────────────────
// Must be registered before any middleware groups that add CSRF verification.

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('stripe',  [WebhookController::class, 'stripe'])->name('stripe');
    Route::post('paypal',  [WebhookController::class, 'paypal'])->name('paypal');
});

// ─── Public Donation Form ─────────────────────────────────────────────────────

Route::prefix('donate')->name('donations.')->group(function () {
    Route::get('/',                      [DonationController::class, 'form'])->name('form');
    Route::post('/',                     [DonationController::class, 'initiate'])->name('initiate');

    // PayPal redirect return/cancel (Stripe uses webhooks only)
    Route::get('paypal/return',          [DonationController::class, 'paypalReturn'])->name('paypal.return');
    Route::get('paypal/cancel',          [DonationController::class, 'paypalCancel'])->name('paypal.cancel');
    Route::get('paypal/subscription/return', [DonationController::class, 'paypalSubscriptionReturn'])->name('paypal.subscription.return');

    Route::get('thank-you',              [DonationController::class, 'thankYou'])->name('thank-you');
});

// ─── Member Portal ────────────────────────────────────────────────────────────

Route::prefix('portal/donations')
    ->name('portal.donations.')
    ->middleware(['auth', 'verified', 'portal.access'])
    ->group(function () {
        Route::get('/',                  [DonationPortalController::class, 'index'])->name('index');
        Route::get('{donation}/receipt', [DonationPortalController::class, 'downloadReceipt'])->name('receipt.download');
        Route::delete('plans/{plan}',    [DonationPortalController::class, 'cancelPlan'])->name('plan.cancel');
    });

// ─── Admin Panel ──────────────────────────────────────────────────────────────

Route::prefix('admin/donations')
    ->name('admin.donations.')
    ->middleware(['auth', 'verified', 'role:super-admin|admin'])
    ->group(function () {
        // Donations index + detail
        Route::get('/',                  [DonationsAdminController::class, 'index'])->name('index');
        Route::get('{donation}',         [DonationsAdminController::class, 'show'])->name('show');

        // Receipt management
        Route::post('{donation}/resend-receipt', [DonationsAdminController::class, 'resendReceipt'])->name('receipt.resend');

        // Recurring plans
        Route::get('plans',              [DonationsAdminController::class, 'plans'])->name('plans.index');
        Route::delete('plans/{plan}',    [DonationsAdminController::class, 'cancelPlan'])->name('plans.cancel');

        // Export
        Route::get('export/csv',         [DonationsAdminController::class, 'exportCsv'])->name('export.csv');
    });
