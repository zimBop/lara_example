<?php

namespace App\Policies;

use App\Models\Driver;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    public function access(Driver $authorizedDriver, Driver $requestedDriver)
    {
        return $authorizedDriver->is($requestedDriver);
    }
}
