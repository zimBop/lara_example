<?php

namespace App\Http\Resources;

use App\Services\AvatarService;
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
            Client::FIRST_NAME => $this->when(
                is_driver() && $this->first_name,
                substr($this->first_name, 0, 1) . '.',
                $this->first_name
            ),
            Client::LAST_NAME => $this->when(
                is_driver() && $this->last_name,
                substr($this->last_name, 0, 1) . '.',
                $this->last_name
            ),
            Client::BIRTHDAY => $this->birthday ? $this->birthday->format('m/d/Y') : null,
            Client::EMAIL => $this->email,
            Client::RATING => $this->rating,
            Client::CO2_SUM => $this->co2_sum,
            Client::FREE_TRIPS => $this->free_trips,
            'avatar' => $this->avatar_url,
        ];
    }
}
