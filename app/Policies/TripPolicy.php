<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Trip;
use Illuminate\Auth\Access\HandlesAuthorization;

class TripPolicy
{
    use HandlesAuthorization;

    /**
     * @param Client $client
     * @param Trip $trip
     */
    public function view(Client $client, Trip $trip)
    {
        return $trip->client_id === $client->id;
    }
}
