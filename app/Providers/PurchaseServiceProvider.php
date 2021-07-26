<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        $this->app->singleton('PurchaseReturnService', function ($app) {
            return new \App\Services\PurchaseReturnService($app);
        });
        

        $this->app->singleton('MaterialService', function ($app) {
            return new \App\Services\MaterialService($app);
        });

        $this->app->singleton('UserService', function ($app) {
            return new \App\Services\UserService($app);
        });

        $this->app->singleton('BranchService', function ($app) {
            return new \App\Services\BranchService($app);
        });

        $this->app->singleton('TransactionService', function ($app) {
            return new \App\Services\TransactionService($app);
        });

        $this->app->singleton('ProductService', function ($app) {
            return new \App\Services\ProductService($app);
        });

        $this->app->singleton('ProductMaterialService', function ($app) {
            return new \App\Services\ProductMaterialService($app);
        });

        $this->app->singleton('ImportService', function ($app) {
            return new \App\Services\ImportService($app);
        });

        $this->app->singleton('ImportMaterialService', function ($app) {
            return new \App\Services\ImportMaterialService($app);
        });

        $this->app->singleton('SupplierService', function ($app) {
            return new \App\Services\SupplierService($app);
        });

        $this->app->singleton('ClosingService', function ($app) {
            return new \App\Services\ClosingService($app);
        });

        $this->app->singleton('OrderService', function ($app) {
            return new \App\Services\OrderService($app);
        });

        $this->app->singleton('ImportConversionService', function ($app) {
            return new \App\Services\ImportConversionService($app);
        });

        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
