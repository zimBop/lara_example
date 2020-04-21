<?php

namespace App\Http\Resources;

use App\Models\Place;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
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
            Place::ID => $this->id,
            Place::NAME => $this->name,
            Place::PLACE_ID => $this->place_id,
            Place::DESCRIPTION => $this->description,
        ];
    }
}
