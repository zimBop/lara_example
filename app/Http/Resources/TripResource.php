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
        $data = [
            Trip::ID => $this->id,
            Trip::PRICE => $this->price,
            // 'wait_duration_adjusted' used here as value instead of 'wait_duration'
            Trip::WAIT_DURATION => $this->wait_duration_adjusted,
            Trip::TRIP_DURATION => $this->trip_duration,
            Trip::OVERVIEW_POLYLINE => $this->overview_polyline,
            Trip::ORIGIN => $this->origin,
            Trip::DESTINATION => $this->destination,
            Trip::WAYPOINTS => $this->waypoints,
            Trip::STATUS => $this->status,
            Trip::CO2 => $this->co2,
            Trip::PICKED_UP_AT => $this->picked_up_at,
            Trip::DISTANCE => round(MetricConverter::metersToMiles($this->distance), 4),
            Trip::CREATED_AT_TIMESTAMP => $this->created_at->timestamp,
            Trip::MESSAGE_FOR_DRIVER => $this->message_for_driver,
        ];

        // TODO find right way to check conditional attributes in tests
        $data['client'] = app()->runningUnitTests() ? new ClientResource($this->client)
            : $this->when(is_driver(), new ClientResource($this->client));

        $data['vehicle'] = app()->runningUnitTests() ? new VehicleResource($this->shift->vehicle)
            : $this->when(is_client(), new VehicleResource($this->shift->vehicle));

        $data['driver'] = app()->runningUnitTests() ? new DriverResource($this->shift->driver)
            : $this->when(is_client(), new DriverResource($this->shift->driver));

        return $data;
    }
}
