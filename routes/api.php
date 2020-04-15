<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('oauth.providers')->post('oauth/token', 'Api\TokenController@getAccessToken');

Route::post('/clients', 'Api\ClientController@store')->name('clients.store');

Route::middleware('multiauth:client', 'scope:access-client', 'can:access,client')->group(function () {
    Route::post('/clients/logout/{client}', 'Api\ClientController@logout')->name('clients.logout');

    Route::post('/clients/forgot-password/{client}', 'Api\ClientController@forgotPassword')->name('clients.forgot-password');
    Route::patch('/clients/reset-password/{client}', 'Api\ClientController@resetPassword')->name('clients.reset-password');

    Route::get('/stripe/ephemeral-key/{client}', 'Api\StripeController@getEphemeralKey')->name('stripe.ephemeral-key');
    Route::get('/stripe/payment-intent/{client}', 'Api\StripeController@getPaymentIntent')->name('stripe.payment-intent');

    Route::apiResource('clients', 'Api\ClientController')->except('index', 'store');
});

Route::post('/drivers/forgot-password', 'Api\DriverController@forgotPassword')->name('drivers.forgot-password');
Route::patch('/drivers/reset-password', 'Api\DriverController@resetPassword')->name('drivers.reset-password');

Route::middleware('multiauth:driver', 'scope:access-driver', 'can:access,driver')->group(function () {
    Route::post('/drivers/logout/{driver}', 'Api\DriverController@logout')->name('drivers.logout');
});
