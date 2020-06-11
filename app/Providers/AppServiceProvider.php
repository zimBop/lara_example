<?php

namespace App\Providers;

use App\Http\Resources\TripOrderResource;
use App\Services\NexmoService;
use App\Services\ResetPasswordService;
use App\Services\ClientService;
use App\Services\ScheduleService;
use App\Services\ShiftService;
use App\Services\StripeService;
use App\Services\TripService;
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
        ClientService::class => ClientService::class,
        TripService::class => TripService::class,
        ScheduleService::class => ScheduleService::class,
        ShiftService::class => ShiftService::class,
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
        TripOrderResource::withoutWrapping();
    }
}
