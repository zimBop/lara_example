<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\ScheduleGap;
use App\Models\ScheduleShift;
use App\Models\Shift;
use App\Models\Trip;
use Illuminate\Support\Collection;

class StatsService
{
    /**
     * @param Collection $scheduleShifts
     * @return int
     */
    public function getTips(Collection $scheduleShifts): int
    {
        $realShifts = $this->getRealShifts($scheduleShifts);

        return $this->getTrips($realShifts)->reduce(
            static function (int $tips, Trip $trip) {
                return $tips + $trip->tips_amount;
            },
            0
        );
    }

    /**
     * @param Collection $realShifts
     * @return mixed
     */
    protected function getTrips(Collection $realShifts)
    {
        return $realShifts->reduce(
            static function (Collection $trips, Shift $shift) {
                return $trips->merge($shift->trips);
            },
            collect([])
        );
    }

    /**
     * @param Collection $scheduleShifts
     * @return mixed
     */
    protected function getRealShifts(Collection $scheduleShifts)
    {
        return $scheduleShifts->reduce(
            static function (Collection $shifts, ScheduleShift $scheduleShift) {
                return $shifts->merge($scheduleShift->shifts);
            },
            collect([])
        );
    }

    /**
     * @param Driver $driver
     * @param Collection $weeks
     * @param ScheduleService $scheduleService
     * @return array
     */
    public function getDriverStats(Driver $driver, Collection $weeks, ScheduleService $scheduleService): array
    {
        return $weeks->map(
            function ($week) use ($scheduleService, $driver) {
                $summary = [
                    'week_earning' => 0,
                    'week_tips' => 0,
                    'week_trips' => 0,
                    'week_shifts' => 0
                ];

                $details = [];

                foreach ($week->gaps as $gap) {
                    $this->addStatsForWeekDay($driver, $gap, $summary, $details);
                }

                return [
                    'title' => $scheduleService->getWeekTitle($week),
                    'summary' => $summary,
                    'details' => $details
                ];
            }
        )->toArray();
    }

    /**
     * @param Driver $driver
     * @param ScheduleGap $gap
     * @param array $summary
     * @param array $details
     */
    protected function addStatsForWeekDay(
        Driver $driver,
        ScheduleGap $gap,
        array &$summary,
        array &$details
    ): void {
        $dayOfWeekName = now()->startOfWeek()->addDays($gap->week_day - 1)->format('l');

        $scheduleShift = $gap->shifts()->whereDriverId($driver->id)->first();

        if ($scheduleShift) {
            $summary['week_shifts']++;
        }

        $realShiftsOfWeekDay = $scheduleShift ? $scheduleShift->shifts : null;
        $tripsOfWeekDay = $realShiftsOfWeekDay ? $this->getTrips($realShiftsOfWeekDay) : null;

        if ($tripsOfWeekDay === null || $tripsOfWeekDay->isEmpty()) {
            if (!isset($details[$dayOfWeekName])) {
                $details[$dayOfWeekName] = null;
            }
            return;
        }

        $details[$dayOfWeekName] = $this->getTripsStats($tripsOfWeekDay);
        $summary['week_earning'] += $details[$dayOfWeekName]['earned'];
        $summary['week_tips'] += $details[$dayOfWeekName]['tips'];
        $summary['week_trips'] += $details[$dayOfWeekName]['trips_count'];
    }

    /**
     * @param Collection $trips
     * @return array
     */
    public function getTripsStats(Collection $trips): array
    {
        $earned = $trips->reduce(function($earned, $trip) {
            return [
                'earned' => $earned['earned'] + $trip->price,
                'tips' => $earned['tips'] + $trip->tips_amount,
            ];
        }, ['earned' => 0, 'tips' => 0]);

        return array_merge(['trips_count' => $trips->count()], $earned);
    }
}
