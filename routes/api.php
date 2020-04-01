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

Route::middleware('auth:api', 'scope:access-client')->group(function () {
    Route::post('/clients/logout/{client}', 'Api\ClientController@logout')->name('clients.logout');

    Route::apiResource('clients', 'Api\ClientController')->except('index', 'store', 'destroy');
});
