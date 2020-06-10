<?php

namespace App\Constants;

class DriverMessages
{
    public const SCHEDULE_NOT_FOUND = 'Schedule for this day not found.';
    public const SCHEDULE_CITY_IS_NOT_SET = 'Can\'t detect city from schedule.';
    public const SHIFT_NOT_FOUND = 'Active shift not found.';
    public const LOCATION_UPDATED = 'Location has updated.';
    public const SHIFT_FINISHED = 'Shift finished.';
    public const CANNOT_STOP_ACTIVE_TRIP = 'Cannot stop shift. The trip is in progress.';
    public const VEHICLE_WASHED = 'Vehicle is washed.';
}
