<?php

namespace App\Policies;

use App\Models\Trip;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class TripPolicy
{
    use HandlesAuthorization;

    /**
     * @param Model $model
     * @param Trip $trip
     * @return bool
     */
    public function view(Model $model, Trip $trip)
    {
        if (is_client()) {
            return $trip->client_id === $model->id;
        }

        return $trip->shift->driver_id === $model->id;
    }
}
