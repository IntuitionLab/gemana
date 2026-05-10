<?php

namespace App\Modules\Donations;

use App\Modules\Donations\Gateways\PayPalGateway;
use App\Modules\Donations\Gateways\StripeGateway;
use App\Modules\Donations\Services\DonationService;
use App\Modules\Donations\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind individual gateways as singletons
        $this->app->singleton(StripeGateway::class);
        $this->app->singleton(PayPalGateway::class);

        // Bind the unified gateway service
        $this->app->singleton(PaymentGatewayService::class, function ($app) {
            return new PaymentGatewayService(
                $app->make(StripeGateway::class),
                $app->make(PayPalGateway::class),
            );
        });

        // Bind the donation service
        $this->app->singleton(DonationService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadViewsFrom(__DIR__.'/Views', 'donations');

        Route::middleware('web')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
            });
    }
}
