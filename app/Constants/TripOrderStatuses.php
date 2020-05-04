<?php

namespace App\Constants;

class TripOrderStatuses extends Constants
{
    public const WAITING_FOR_CONFIRMATION = 1;
    public const SEARCHING_CAR = 2;
    public const CAR_ON_THE_ROAD = 3;
    public const CAR_IS_WAITING_CLIENT = 4;
}
