<?php

namespace App\Providers;

use App\Services\NexmoService;
use App\Services\ResetPasswordService;
use Illuminate\Support\ServiceProvider;
use Nexmo\Client as NexmoClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NexmoService::class, function ($app) {
            return new NexmoService($app->make(NexmoClient::class));
        });

        $this->app->singleton(ResetPasswordService::class, function ($app) {
            return new ResetPasswordService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
