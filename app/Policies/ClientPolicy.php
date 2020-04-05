<?php

namespace App\Policies;

use App\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function access(Client $authorizedClient, Client $requestedClient)
    {
        return $authorizedClient->id === $requestedClient->id;
    }
}
