<?php

namespace App\Http\Resources;

use App\Models\Driver;
use App\Models\Shift;
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
            Driver::ID => $this->id,
            'full_name' => $this->full_name,
            'image' => $this->avatar ? $this->avatar_url : 'https://electra.ag.digital/storage/driver.png',
            Driver::RATING => $this->rating,
            // Driver::PHONE return only digits
            Driver::PHONE => '18152478181',
            'has_active_shift' => (boolean)$this->active_shift,
            Shift::WASHED_AT => $this->when($this->active_shift, function () {
                return $this->active_shift->washed_at;
            }),
        ];
    }
}
