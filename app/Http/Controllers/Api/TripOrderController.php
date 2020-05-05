<?php

namespace App\Http\Controllers\Api;

use App\Constants\TripOrderStatuses;
use App\Http\Requests\TripOrder\ConfirmTripOrderRequest;
use App\Http\Requests\TripOrder\StoreTripOrderRequest;
use App\Http\Resources\TripOrderResource;
use App\Models\Client;
use App\Models\TripOrder;
use App\Services\TripService;
use Illuminate\Http\Request;

class TripOrderController extends ApiController
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
    public function show(Client $client, TripService $tripService)
    {
        $tripService->setOrder($client->tripOrder)
            ->checkTripOrderExists();

        return $this->data(new TripOrderResource($client->tripOrder));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TripOrder  $tripOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TripOrder $tripOrder)
    {
        //
    }

    /**
     * @param Request $request
     * @param Client $client
     * @param TripService $tripService
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(ConfirmTripOrderRequest $request, Client $client, TripService $tripService)
    {
        $tripService->setOrder($client->tripOrder)
            ->checkTripOrderExists();

        $tripService->checkDriverAvailable();

        $client->tripOrder->update(array_merge(
            [TripOrder::STATUS => TripOrderStatuses::SEARCHING_CAR],
            $request->only([TripOrder::MESSAGE_FOR_DRIVER, TripOrder::PAYMENT_METHOD_ID])
       ));

        return $this->data(new TripOrderResource($client->tripOrder));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TripOrder  $tripOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(TripOrder $tripOrder)
    {
        //
    }
}
