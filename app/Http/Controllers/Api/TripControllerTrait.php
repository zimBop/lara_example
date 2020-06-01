<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TripResource;
use App\Services\TripService;
use Illuminate\Database\Eloquent\Model;

trait TripControllerTrait
{
    protected function changeTripStatus(Model $model, TripService $tripService, $status)
    {
        $trip = $model->active_trip;

        $tripService->checkTrip($trip, $status);

        $tripService->changeStatus($trip, $status);

        return $this->data(new TripResource($trip));
    }
}
