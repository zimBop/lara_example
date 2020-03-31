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

Route::post('/clients', 'Api\ClientController@store');

Route::middleware('auth:api', 'scope:access-client')->group(function () {
    Route::apiResource('clients', 'Api\ClientController')->except('store');
});
