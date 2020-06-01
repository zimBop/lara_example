<?php

namespace App\Http\Controllers\Api;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Requests\Client\RateDriverRequest;
use App\Http\Resources\TripResource;
use App\Models\Client;
use App\Models\Tip;
use App\Models\Trip;
use App\Notifications\TripOrderCanceled;
use App\Services\StripeService;
use App\Services\TripService;

class ClientTripController extends ApiController
{
    use TripControllerTrait;

    /**
     * @param Client $client
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Client $client)
    {
        return TripResource::collection($client->trips()->archived()->paginate(20));
    }

    /**
     * @param Trip $trip
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Client $client, Trip $trip)
    {
        return $this->data(new TripResource($trip));
    }

    public function cancel(Client $client)
    {
        $tripOrder = $client->tripOrder;

        if (!$tripOrder) {
            return $this->error(TripMessages::REQUEST_NOT_FOUND);
        }

        if ($tripOrder->status < TripStatuses::TRIP_IN_PROGRESS) {

            foreach ($tripOrder->shifts as $shift) {
                $shift->driver->notify(new TripOrderCanceled($tripOrder->id));
            }

            $tripOrder->delete();

            if ($client->active_trip) {
                $client->active_trip->delete();
            }

            return $this->done(TripMessages::CANCELED);
        }

        return $this->done(TripMessages::CANNOT_BE_CANCELED);
    }

    public function rate(RateDriverRequest $request, Client $client, TripService $tripService, StripeService $stripeService)
    {
        $trip = Trip::find($request->input('trip_id'));

        $tripService->checkTrip($trip, TripStatuses::TRIP_ARCHIVED);

        $driver = $trip->shift->driver;

        $driver->reviews()->create($request->input());
        $driver->updateRating();

        $tipAmount = $request->input(Tip::AMOUNT);
        $tipPaymentMethod = $request->input(Tip::PAYMENT_METHOD_ID);
        if ($tipAmount && $tipPaymentMethod) {

            $stripeError = $stripeService->setClient($client)
                ->makePayment($trip, 'Tips for Driver', $tipAmount, $tipPaymentMethod);

            if (!$stripeError) {
                $driver->tips()->create($request->input());
            }
        }

        $tripService->changeStatus($trip, TripStatuses::TRIP_ARCHIVED);

        return $this->done(TripMessages::DRIVER_RATED);
    }

    public function archive(Client $client, TripService $tripService)
    {
        return $this->changeTripStatus($client, $tripService, TripStatuses::TRIP_ARCHIVED);
    }
}
