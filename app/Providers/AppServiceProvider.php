<?php

namespace App\Providers;

use App\Services\NexmoService;
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
