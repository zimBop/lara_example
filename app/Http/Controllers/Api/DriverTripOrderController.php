<?php

namespace App\Http\Controllers\Api;

use App\Constants\DriverMessages;
use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Resources\TripOrderResource;
use App\Http\Resources\TripResource;
use App\Models\Driver;
use App\Models\TripOrder;
use App\Notifications\TripStatusChanged;
use App\Services\TripService;

class DriverTripOrderController extends ApiController
{
    public function showList(Driver $driver)
    {
        $activeShift = $driver->active_shift;

        if (!$activeShift) {
            return $this->done(DriverMessages::SHIFT_NOT_FOUND);
        }

        return $this->data(TripOrderResource::collection($activeShift->trip_orders));
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

        if (!$driver->active_shift->washed_at) {
            return $this->error(TripMessages::WASH_CAR);
        }

        $trip = $tripService->createTrip($tripOrder, $driver);

        $tripService->updateTripOrders($tripOrder->id, $driver->id);

        $tripOrder->shifts()->detach();

        $trip->client->notify(new TripStatusChanged(TripStatuses::DRIVER_IS_ON_THE_WAY, $trip->id));

        $tripOrder->update([TripOrder::STATUS => TripStatuses::DRIVER_IS_ON_THE_WAY]);

        return $this->data(new TripResource($trip));
    }
}
