<?php

namespace App\Http\Controllers\Api;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Requests\TripOrder\ConfirmTripOrderRequest;
use App\Http\Requests\TripOrder\StoreTripOrderRequest;
use App\Http\Resources\TripOrderResource;
use App\Http\Resources\TripResource;
use App\Models\Client;
use App\Models\Driver;
use App\Models\TripOrder;
use App\Notifications\TripStatusChanged;
use App\Services\TripService;

class TripOrderController extends ApiController
{
    /**
     * @param StoreTripOrderRequest $request
     * @param Client $client
     * @param TripService $tripService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTripOrderRequest $request, Client $client, TripService $tripService)
    {
        $tripOrder = $tripService->updateOrCreateTripOrder($request, $client);

        return $this->data(new TripOrderResource($tripOrder));
    }

    /**
     * @param Client $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Client $client)
    {
        if (!$client->tripOrder) {
            return $this->done(TripMessages::REQUEST_NOT_FOUND);
        }

        if ($client->active_trip) {
            return $this->data(new TripResource($client->active_trip));
        }

        return $this->data(new TripOrderResource($client->tripOrder));
    }

    /**
     * @param ConfirmTripOrderRequest $request
     * @param Client $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(ConfirmTripOrderRequest $request, Client $client)
    {
        if (!$client->tripOrder) {
            return $this->done(TripMessages::REQUEST_NOT_FOUND);
        }

        $client->tripOrder->update(array_merge(
            [TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER],
            $request->only([TripOrder::MESSAGE_FOR_DRIVER, TripOrder::PAYMENT_METHOD_ID])
       ));

        return $this->data(new TripOrderResource($client->tripOrder));
    }

    /**
     * @param Driver $driver
     * @param TripOrder $tripOrder
     * @param TripService $tripService
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Driver $driver, TripOrder $tripOrder, TripService $tripService)
    {
        if ((int)$tripOrder->status !== TripStatuses::LOOKING_FOR_DRIVER) {
            return $this->error(TripMessages::REQUEST_ALREADY_ACCEPTED);
        }

        if (!$driver->active_shift) {
            return $this->error(TripMessages::DRIVER_HAS_NOT_SHIFT);
        }

        if ($driver->active_trip) {
            return $this->error(TripMessages::DRIVER_ALREADY_HAS_ACTIVE_TRIP);
        }

        $trip = $tripService->createTrip($tripOrder, $driver);

        $trip->client->notify(new TripStatusChanged(TripStatuses::DRIVER_IS_ON_THE_WAY, $trip->id));

        $tripOrder->update([TripOrder::STATUS => TripStatuses::DRIVER_IS_ON_THE_WAY]);

        return $this->data(new TripResource($trip));
    }
}
