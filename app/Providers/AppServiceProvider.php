<?php

namespace App\Providers;

use App\Services\NexmoService;
use App\Services\ResetPasswordService;
use App\Services\StripeService;
use App\Services\VerificationCodeService;
use Illuminate\Support\ServiceProvider;
use Nexmo\Client as NexmoClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        VerificationCodeService::class => VerificationCodeService::class,
        ResetPasswordService::class => ResetPasswordService::class,
        StripeService::class => StripeService::class,
    ];

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
