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
Route::get('/driver_locations', function () {
    $path = storage_path("logs/driver_locations-" . now()->format('Y-m-d') . ".log");
    return "<pre>" . \Illuminate\Support\Facades\File::get($path) . "</pre>";
});

Route::get('/reset-password-page', 'Web\DriverResetPasswordController')
    ->middleware('throttle:60,1')->name(R_DRIVER_RESET_PASSWORD);

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], static function () {
    Route::get('/login', 'LoginController@showLoginForm')->name(R_ADMIN_LOGIN);
    Route::post('/login', 'LoginController@login')->middleware('throttle:5,1')->name(R_ADMIN_LOGIN_SUBMIT);
    Route::get('/logout', 'LoginController@logout')->name(R_ADMIN_LOGOUT);
    Route::get('/', 'DashboardController@index')->name(R_ADMIN_DASHBOARD);

    Route::middleware('auth:admin')
        ->group(static function () {

            Route::prefix('clients')->group(static function(){
                    Route::get('/', 'ClientController@index')->name(R_ADMIN_CLIENTS_LIST);

                    Route::prefix('ajax')->group(static function(){
                        Route::post('change_activity/{client}', 'ClientController@changeActivity')
                            ->middleware('ajax')
                            ->name(R_ADMIN_AJAX_CLIENTS_CHANGE_ACTIVITY);
                    });
                });

            Route::prefix('drivers')->group(static function(){
                Route::get('/', 'DriverController@index')->name(R_ADMIN_DRIVERS_LIST);
                Route::get('/create', 'DriverController@create')->name(R_ADMIN_DRIVERS_CREATE);
                Route::get('/edit/{driver}', 'DriverController@edit')->name(R_ADMIN_DRIVERS_EDIT);
                Route::post('/store/{driver?}', 'DriverController@store')->name(R_ADMIN_DRIVERS_STORE);
                Route::get('/delete/{driver}', 'DriverController@delete')->name(R_ADMIN_DRIVERS_DELETE);
            });

            Route::prefix('vehicles')
                ->group(static function(){
                    Route::get('/', 'VehicleController@index')->name(R_ADMIN_VEHICLES_LIST);
                    Route::get('/create', 'VehicleController@create')->name(R_ADMIN_VEHICLES_CREATE);
                    Route::get('/edit/{vehicle}', 'VehicleController@edit')->name(R_ADMIN_VEHICLES_EDIT);
                    Route::post('/store/{vehicle?}', 'VehicleController@store')->name(R_ADMIN_VEHICLES_STORE);
                    Route::get('/delete/{vehicle}', 'VehicleController@delete')->name(R_ADMIN_VEHICLES_DELETE);
                });

        });
});
