<?php

namespace App\Http\Controllers\Api;

use App\Constants\TripStatuses;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Trip;
use App\Notifications\DriverArrived;
use App\Services\StripeService;
use App\Services\TripService;
use Illuminate\Http\Request;

class TripController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cancel(Client $client)
    {
        if (!$client->tripOrder) {
            return $this->error('Trip Request not found.');
        }

        if ($client->tripOrder->status < TripStatuses::TRIP_IN_PROGRESS) {
            $client->tripOrder->delete();

            if ($client->active_trip) {
                $client->active_trip->delete();
            }

            return $this->done('Trip canceled.');
        }

        return $this->done('Trip cannot be canceled. Trip in progress.');
    }

    public function arrived(Driver $driver, TripService $tripService)
    {
        $trip = $driver->active_trip;

        $tripService->checkTrip($trip, TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT);

        $tripService->changeStatus($trip, TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT);

        return $this->done("Driver arrived.");
    }

    public function start(Driver $driver, TripService $tripService, StripeService $stripeService)
    {
        $trip = $driver->active_trip;

        $tripService->checkTrip($trip, TripStatuses::TRIP_IN_PROGRESS);

        $stripeService->setClient($trip->client)
            ->makePayment($trip, 'Trip Payment');

        $trip->update([Trip::PICKED_UP_AT => now()]);
        $tripService->changeStatus($trip, TripStatuses::TRIP_IN_PROGRESS);

        return $this->done("Trip progress started.");
    }

    public function finish(Driver $driver, TripService $tripService)
    {
        $trip = $driver->active_trip;

        $tripService->checkTrip($trip, TripStatuses::UNRATED);

        $tripService->changeStatus($trip, TripStatuses::UNRATED);

        return $this->done("Trip finished.");
    }

    public function archive(Client $client, TripService $tripService)
    {
        $trip = $client->active_trip;

        $tripService->checkTrip($trip, TripStatuses::TRIP_ARCHIVED);

        $tripService->changeStatus($trip, TripStatuses::TRIP_ARCHIVED);

        return $this->done("Trip archived.");
    }
}
