<?php

namespace App\Services;

use App\Models\ScheduleShift;
use App\Models\Shift;
use App\Models\Trip;
use Illuminate\Support\Collection;

class DriverService
{
    public function getTips(Collection $scheduleShifts): int
    {
        $realShifts = $scheduleShifts->reduce(
                static function (Collection $shifts, ScheduleShift $scheduleShift) {
                    return $shifts->merge($scheduleShift->shifts);
                },
                collect([])
            );

        return $realShifts->reduce(
                static function (Collection $trips, Shift $shift) {
                    return $trips->merge($shift->trips);
                },
                collect([])
            )->reduce(
                static function (int $tips, Trip $trip) {
                    return $tips + $trip->tips->amount;
                },
                0
            );
    }
}
