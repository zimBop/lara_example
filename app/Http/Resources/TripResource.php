<?php

namespace App\Http\Resources;

use App\Logic\MetricConverter;
use App\Models\Trip;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            Trip::ID => $this->id,
            Trip::PRICE => $this->price,
            Trip::WAIT_DURATION => $this->wait_duration,
            Trip::WAIT_DURATION_ADJUSTED => $this->wait_duration_adjusted,
            Trip::TRIP_DURATION => $this->trip_duration,
            Trip::OVERVIEW_POLYLINE => $this->overview_polyline,
            Trip::ORIGIN => $this->origin,
            Trip::DESTINATION => $this->destination,
            Trip::WAYPOINTS => $this->waypoints,
            Trip::STATUS => $this->status,
            Trip::CO2 => $this->co2,
            Trip::PICKED_UP_AT => $this->picked_up_at,
            Trip::DISTANCE => round(MetricConverter::metersToMiles($this->distance), 4),
            'driver' => new DriverResource($this->shift->driver),
            'vehicle' => new VehicleResource($this->shift->vehicle),
        ];
    }
}
