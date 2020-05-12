<?php

namespace App\Http\Controllers\Api;

use App\Constants\TripStatuses;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Trip;
use App\Notifications\DriverArrived;
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

    public function arrived(Driver $driver)
    {
        $trip = $driver->active_trip;

        if (!$trip) {
            return $this->error('Active trip not found.');
        }

        if ($trip->status !== TripStatuses::DRIVER_IS_ON_THE_WAY) {
            return $this->error('Incorrect trip status.');
        }

        $data = [Trip::STATUS => TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT];

        $trip->update($data);

        $trip->client->tripOrder->update($data);

        $trip->client->notify(new DriverArrived());

        return $this->done("'Driver Arrived' notify sent to the client.");
    }
}
