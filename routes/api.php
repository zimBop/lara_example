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

Route::post('/clients', 'Api\ClientController@store')->name('clients.store');

Route::middleware('auth:api', 'scope:access-client', 'can:access,client')->group(function () {
    Route::post('/clients/logout/{client}', 'Api\ClientController@logout')->name('clients.logout');

    Route::post('/clients/forgot-password/{client}', 'Api\ClientController@forgotPassword')->name('clients.forgot-password');
    Route::patch('/clients/reset-password/{client}', 'Api\ClientController@resetPassword')->name('clients.reset-password');

    Route::get('/clients/stripe-secret/{client}', 'Api\ClientController@getStripeSecret')->name('clients.stripe-secret');

    Route::apiResource('clients', 'Api\ClientController')->except('index', 'store', 'destroy');
});
