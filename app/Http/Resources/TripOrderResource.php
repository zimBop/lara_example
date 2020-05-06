<?php

namespace App\Http\Resources;

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
        ];
    }
}
