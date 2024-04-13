<?php

namespace Wbp\Paypal\Providers;

use Illuminate\Support\ServiceProvider;

class PaypalProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/paypal.php' => config_path('paypal.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/paypal.php', 'paypal'
        );
    }
}
