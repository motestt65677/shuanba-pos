<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PurchaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('PurchaseService', function ($app) {
            return new \App\Services\PurchaseService($app);
        });

        $this->app->singleton('MaterialService', function ($app) {
            return new \App\Services\MaterialService($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
