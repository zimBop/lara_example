<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Driver;
use App\Models\Trip;
use App\Policies\ClientPolicy;
use App\Policies\DriverPolicy;
use App\Policies\TripPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         Client::class => ClientPolicy::class,
         Driver::class => DriverPolicy::class,
         Trip::class => TripPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes(null, [
            'prefix' => 'api/oauth',
        ]);

        Route::group(['middleware' => 'oauth.providers'], function () {
            Passport::routes(
                function ($router) {
                    return $router->forAccessTokens();
                },
                ['prefix' => 'api/oauth']
            );
        });

        Passport::tokensCan([
            'access-client' => 'Access client related endpoints.',
            'access-driver' => 'Access driver related endpoints.',
        ]);
    }
}
