<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], static function () {
    Route::get('/login', 'LoginController@showLoginForm')->name(R_ADMIN_LOGIN);
    Route::post('/login', 'LoginController@login')->middleware('throttle:5,1')->name(R_ADMIN_LOGIN_SUBMIT);
    Route::get('/logout', 'LoginController@logout')->name(R_ADMIN_LOGOUT);
    Route::get('/', 'DashboardController@index')->name(R_ADMIN_DASHBOARD);

    Route::middleware('auth:admin')
        ->group(static function () {

            Route::prefix('clients')
                ->group(static function(){
                    Route::get('/', 'ClientController@index')->name(R_ADMIN_CLIENTS_LIST);
                });

            Route::prefix('vehicles')
                ->group(static function(){
                    Route::get('/', 'VehicleController@index')->name(R_ADMIN_VEHICLES_LIST);
                });

        });
});
