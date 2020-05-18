<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Client;

class ClientResource extends JsonResource
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
            Client::ID => $this->id,
            Client::PHONE => $this->phone,
            Client::FIRST_NAME => $this->first_name,
            Client::LAST_NAME => $this->last_name,
            Client::BIRTHDAY => $this->birthday ? $this->birthday->format('m/d/Y') : null,
            Client::EMAIL => $this->email,
            Client::RATING => $this->rating,
            Client::CO2_SUM => $this->co2_sum,
        ];
    }
}
