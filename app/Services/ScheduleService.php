<?php

namespace App\Services;

use App\Http\Requests\Admin\UpdateScheduleRequest;
use App\Models\ScheduleGap;
use App\Models\ScheduleShift;
use App\Models\ScheduleWeek;

class ScheduleService
{
    /**
     * @return ScheduleWeek|null
     */
    public function getFirstWeek(): ?ScheduleWeek
    {
        return ScheduleWeek::orderBy(ScheduleWeek::YEAR)
            ->orderBy(ScheduleWeek::NUMBER)
            ->first();
    }

    public function isNextWeekExists()
    {
        $now = now();

        return ScheduleWeek::where([
            ScheduleWeek::YEAR => $now->year,
            ScheduleWeek::NUMBER => now()->addWeek()->weekOfYear,
        ])->exists();
    }

    public function getTimeSelectOptions()
    {
        return array_reduce(
            range(0, 23),
            static function (array $options, int $hours) {
                $date = now()->startOfDay()->addHours($hours);
                $options[$date->format('H:i:s')] = $date->format('g A');

                return $options;
            },
            []
        );
    }

    public function updateWeekGaps(UpdateScheduleRequest $request, ScheduleWeek $week)
    {
        $startTimes = $request->input('start');
        $endTimes = $request->input('end');
        $week->gaps->each(function($gap) use ($startTimes, $endTimes) {
            if ($gap->start !== $startTimes[$gap->id] ||
                    $gap->end !== $endTimes[$gap->id]) {
                $gap->update([
                    ScheduleGap::START => $startTimes[$gap->id],
                    ScheduleGap::END => $endTimes[$gap->id],
                ]);
            }
        });
    }

    public function updateWeekShifts(UpdateScheduleRequest $request, ScheduleWeek $week)
    {
        $drivers = $request->input('drivers');
        $cities = $request->input('cities');
        $week->shifts->each(function($shift) use ($drivers, $cities) {
            if (isset($drivers[$shift->id]) && $drivers[$shift->id]
                    && $shift->driver_id !== $drivers[$shift->id]) {
                $shift->update([
                    ScheduleShift::DRIVER_ID => $drivers[$shift->id],
                ]);
            }

            if (isset($cities[$shift->id]) && $shift->city_id !== $cities[$shift->id]) {
                $shift->update([
                    ScheduleShift::CITY_ID => $cities[$shift->id],
                ]);
            }
        });
    }
}
