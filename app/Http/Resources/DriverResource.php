<?php

namespace App\Http\Resources;

use App\Models\Driver;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
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
            'full_name' => $this->full_name,
            'image' => 'https://electra.ag.digital/storage/driver.svg',
            Driver::RATING => $this->rating,
            // Driver::PHONE return only digits
            Driver::PHONE => '18152478181',
        ];
    }
}
