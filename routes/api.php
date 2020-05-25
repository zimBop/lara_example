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

Route::middleware('multiauth:client', 'scope:access-client')->group(function () {
    Route::middleware('can:access,client')->group(function () {
        Route::post('/clients/logout/{client}', 'Api\ClientController@logout')->name('clients.logout');

        Route::post('/clients/forgot-password/{client}', 'Api\ClientController@forgotPassword')->name(
            'clients.forgot-password'
        );
        Route::patch('/clients/reset-password/{client}', 'Api\ClientController@resetPassword')->name(
            'clients.reset-password'
        );

        Route::post('/clients/send-code/{client}', 'Api\ClientController@sendCode')->name(
            'clients.send-code'
        );
        Route::patch('/clients/change-phone/{client}', 'Api\ClientController@changePhone')->name(
            'clients.change-phone'
        );

        Route::post('/clients/invite-friend/{client}', 'Api\ClientController@inviteFriend')->name(
            'clients.invite-friend'
        );

        Route::get('/stripe/ephemeral-key/{client}', 'Api\StripeController@getEphemeralKey')->name(
            'stripe.ephemeral-key'
        );
        Route::get('/stripe/payment-intent/{client}', 'Api\StripeController@getPaymentIntent')->name(
            'stripe.payment-intent'
        );

        Route::apiResource('clients', 'Api\ClientController')->except('index', 'store');
        Route::apiResource('clients.places', 'Api\PlaceController')->except('update');

        Route::post('clients/{client}/trip-request', 'Api\TripOrderController@store')->name('trip-order.store');
        Route::get('clients/{client}/trip-request', 'Api\TripOrderController@show')->name('trip-order.show');
        Route::post('clients/{client}/trip-request/confirm', 'Api\TripOrderController@confirm')->name('trip-order.confirm');

        Route::post('clients/{client}/trip/cancel', 'Api\TripController@cancel')->name('trip.cancel');
        Route::post('clients/{client}/trip/rate', 'Api\TripController@rate')->name('trip.rate');
        Route::post('clients/{client}/trip/archive', 'Api\TripController@archive')->name('trip.archive');

        Route::get('clients/{client}/trips', 'Api\TripController@index')->name('clients.trips.index');
        Route::get('clients/{client}/trips/{trip}', 'Api\TripController@show')->name('clients.trips.show')
            ->middleware('can:view,trip');
    });

    Route::get('/places-autocomplete', 'Api\Google\PlacesAutocompleteController')->name('google.places-autocomplete');
    Route::get('/reverse-geocoding', 'Api\Google\ReverseGeocodingController')->name('google.reverse-geocoding');
});

Route::post('/drivers/forgot-password', 'Api\DriverController@forgotPassword')->name('drivers.forgot-password');
Route::patch('/drivers/reset-password', 'Api\DriverController@resetPassword')->name('drivers.reset-password');

Route::middleware('multiauth:driver', 'scope:access-driver', 'can:access,driver')->group(function () {
    Route::get('/drivers/{driver}', 'Api\DriverController@show')->name('drivers.show');
    Route::post('/drivers/logout/{driver}', 'Api\DriverController@logout')->name('drivers.logout');

    Route::get('/drivers/{driver}/trip-request/{tripOrder}', 'Api\TripOrderController@showById')
        ->name('trip-order.show-by-id');
    Route::post('/drivers/{driver}/trip-request/{tripOrder}/accept', 'Api\TripOrderController@accept')
        ->name('trip-order.accept');

    Route::get('/drivers/{driver}/trip', 'Api\TripController@showActiveTripForDriver')->name('trip.show-for-driver');
    Route::post('/drivers/{driver}/trip/arrived', 'Api\TripController@arrived')->name('trip.arrived');
    Route::post('/drivers/{driver}/trip/start', 'Api\TripController@start')->name('trip.start');
    Route::post('/drivers/{driver}/trip/finish', 'Api\TripController@finish')->name('trip.finish');

    Route::post('/drivers/{driver}/shift/start', 'Api\ShiftController@start')->name('shift.start');
    Route::post('/drivers/{driver}/shift/finish', 'Api\ShiftController@finish')->name('shift.finish');
});

Route::middleware('multiauth:driver,client', 'scope:access-driver,access-client')->group(function () {
    Route::post('devices', 'Api\DeviceController@store')->name('devices.store');
    Route::delete('devices', 'Api\DeviceController@destroy')->name('devices.delete');
});
