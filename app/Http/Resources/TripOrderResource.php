<?php

namespace App\Http\Resources;

use App\Constants\TripStatuses;
use App\Logic\MetricConverter;
use App\Models\TripOrder;
use Illuminate\Http\Resources\Json\JsonResource;

class TripOrderResource extends JsonResource
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
            TripOrder::ID => $this->id,
            TripOrder::PRICE => $this->price,
            TripOrder::WAIT_DURATION => $this->wait_duration,
            TripOrder::TRIP_DURATION => $this->trip_duration,
            TripOrder::OVERVIEW_POLYLINE => $this->overview_polyline,
            TripOrder::ORIGIN => $this->origin,
            TripOrder::DESTINATION => $this->destination,
            TripOrder::WAYPOINTS => $this->waypoints,
            TripOrder::STATUS => $this->status,
            TripOrder::DISTANCE => round(MetricConverter::metersToMiles($this->distance), 4),
            TripOrder::MESSAGE_FOR_DRIVER => $this->message_for_driver,
            'client' => $this->when(is_driver(), new ClientResource($this->client)),
            TripOrder::IS_FREE_TRIP => $this->is_free_trip,
        ];
    }
}
