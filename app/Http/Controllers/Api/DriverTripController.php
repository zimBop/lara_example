<?php

namespace App\Http\Controllers\Api;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Resources\TripResource;
use App\Models\Driver;
use App\Models\Shift;
use App\Models\Trip;
use App\Notifications\TripCanceled;
use App\Services\StripeService;
use App\Services\TripService;

class DriverTripController extends ApiController
{
    use TripControllerTrait;

    public function showActiveTrip(Driver $driver)
    {
        if (!$driver->active_trip) {
            return $this->done(TripMessages::TRIP_NOT_FOUND);
        }

        return $this->data(new TripResource($driver->active_trip));
    }

    public function cancel(Driver $driver)
    {
        $trip = $driver->active_trip;

        if (!$trip) {
            return $this->error(TripMessages::REQUEST_NOT_FOUND);
        }

        if ($trip->status < TripStatuses::TRIP_IN_PROGRESS) {

            $client = $trip->client;

            $client->notify(new TripCanceled($trip->id));

            $client->tripOrder->delete();
            $trip->delete();

            return $this->done(TripMessages::CANCELED);
        }

        return $this->done(TripMessages::CANNOT_BE_CANCELED);
    }

    public function arrived(Driver $driver, TripService $tripService)
    {
        return $this->changeTripStatus($driver, $tripService, TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT);
    }

    public function start(Driver $driver, TripService $tripService, StripeService $stripeService)
    {
        $trip = $driver->active_trip;

        $tripService->checkTrip($trip, TripStatuses::TRIP_IN_PROGRESS);

        $stripeError = $stripeService->setClient($trip->client)
            ->makePayment($trip, 'Trip Payment');

        if ($stripeError) {
            return $this->error($stripeError);
        }

        $trip->update([Trip::PICKED_UP_AT => now()]);
        $tripService->changeStatus($trip, TripStatuses::TRIP_IN_PROGRESS);

        return $this->data(new TripResource($trip));
    }

    public function finish(Driver $driver, TripService $tripService)
    {
        $driver->active_shift->update([
            Shift::WASHED_AT => null
        ]);

        return $this->changeTripStatus($driver, $tripService, TripStatuses::UNRATED);
    }
}
