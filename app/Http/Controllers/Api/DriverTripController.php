<?php

namespace App\Http\Controllers\Api;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Requests\Driver\RateClientRequest;
use App\Http\Resources\TripResource;
use App\Models\Driver;
use App\Models\Review;
use App\Models\Shift;
use App\Models\Trip;
use App\Notifications\TripCanceled;
use App\Services\StripeService;
use App\Services\TripService;

class DriverTripController extends ApiController
{
    use TripControllerTrait;

    /**
     * @param Driver $driver
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Driver $driver)
    {
        $trips = Trip::whereIn(Trip::SHIFT_ID, $driver->shifts->pluck('id'))
            ->where(Trip::STATUS, '>', TripStatuses::TRIP_IN_PROGRESS)
            ->latest()->paginate(20);

        return TripResource::collection($trips);
    }

    /**
     * @param Driver $driver
     * @param Trip $trip
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Driver $driver, Trip $trip)
    {
        return $this->data(new TripResource($trip));
    }

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

        if ($trip->status >= TripStatuses::TRIP_IN_PROGRESS) {
            return $this->done(TripMessages::CANNOT_BE_CANCELED);
        }

        $client = $trip->client;

        $client->notify(new TripCanceled($trip->id));

        $client->tripOrder->delete();
        $trip->delete();

        return $this->done(TripMessages::CANCELED);
    }

    public function arrived(Driver $driver, TripService $tripService)
    {
        return $this->changeTripStatus($driver, $tripService, TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT);
    }

    public function start(Driver $driver, TripService $tripService, StripeService $stripeService)
    {
        $trip = $driver->active_trip;

        $tripService->checkTrip($trip, TripStatuses::TRIP_IN_PROGRESS);

        if ($trip->is_free_trip) {
            $tripService->processFreeTrip($trip);
        } else {
            $stripeError = $stripeService->setClient($trip->client)
                ->makePayment($trip, 'Trip Payment');

            if ($stripeError) {
                return $this->error($stripeError);
            }
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

    public function rate(RateClientRequest $request, Driver $driver)
    {
        $trip = Trip::findOrFail($request->input('trip_id'));

        if ($trip->status < TripStatuses::UNRATED) {
            return $this->error(TripMessages::INCORRECT_STATUS);
        }

        $client = $trip->client;

        $client->reviews()->updateOrCreate(
            [Review::TRIP_ID => $trip->id],
            $request->input()
        );
        $client->updateRating();

        return $this->done(TripMessages::CLIENT_RATED);
    }
}
