<?php

namespace App\Http\Resources;

use App\Models\Vehicle;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            Vehicle::BRAND => $this->brand,
            Vehicle::MODEL => $this->model,
            Vehicle::LICENSE_PLATE => $this->license_plate,
            'color' => $this->color_data,
        ];
    }
}
