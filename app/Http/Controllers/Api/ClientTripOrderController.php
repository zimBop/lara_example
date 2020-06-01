<?php

namespace App\Http\Controllers\Api;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Requests\TripOrder\ConfirmTripOrderRequest;
use App\Http\Requests\TripOrder\StoreTripOrderRequest;
use App\Http\Resources\TripOrderResource;
use App\Http\Resources\TripResource;
use App\Models\Client;
use App\Models\TripOrder;
use App\Services\TripService;

class ClientTripOrderController extends ApiController
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
     * @param TripService $tripService
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(ConfirmTripOrderRequest $request, Client $client, TripService $tripService)
    {
        if (!$client->tripOrder) {
            return $this->done(TripMessages::REQUEST_NOT_FOUND);
        }

        $tripOrder = $client->tripOrder;

        if ((int)$tripOrder->status !== TripStatuses::WAITING_FOR_CONFIRMATION) {
            return $this->error(TripMessages::REQUEST_ALREADY_CONFIRMED);
        }

        $tripOrder->update(array_merge(
            [TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER],
            $request->only([TripOrder::MESSAGE_FOR_DRIVER, TripOrder::PAYMENT_METHOD_ID])
       ));

        $tripService->informDriversAboutOrder($tripOrder);

        return $this->data(new TripOrderResource($tripOrder->refresh()));
    }
}
