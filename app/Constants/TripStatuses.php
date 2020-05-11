<?php

namespace App\Constants;

class TripStatuses extends Constants
{
    public const WAITING_FOR_CONFIRMATION = 1;
    public const LOOKING_FOR_DRIVER = 2;
    public const DRIVER_IS_ON_THE_WAY = 3;
    public const DRIVER_IS_WAITING_FOR_CLIENT = 4;
    public const TRIP_IN_PROGRESS = 5;
    public const UNRATED = 6;
    public const TRIP_ARCHIVED = 7;
}
