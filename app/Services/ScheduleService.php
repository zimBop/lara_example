<?php

namespace App\Services;

use App\Filters\ScheduleWeekFilter;
use App\Http\Requests\Admin\GetScheduleRequest;
use App\Http\Requests\Admin\UpdateScheduleRequest;
use App\Models\Driver;
use App\Models\ScheduleGap;
use App\Models\ScheduleShift;
use App\Models\ScheduleWeek;
use Illuminate\Support\Carbon;

class ScheduleService
{
    /**
     * @param bool $first
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    protected function getBoundaryWeek(bool $first = true)
    {
        $direction = $first ? 'asc' : 'desc';

        return ScheduleWeek::orderBy(ScheduleWeek::YEAR, $direction)
            ->orderBy(ScheduleWeek::NUMBER, $direction)
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
        $week->shifts->each(static function($shift) use ($drivers, $cities) {
            $intDriverId = (int)$drivers[$shift->id];
            $driverId = $intDriverId ?: null;

            if ($shift->driver_id !== $driverId) {
                $shift->update([
                    ScheduleShift::DRIVER_ID => $driverId,
                ]);
            }

            if (isset($cities[$shift->id]) && $shift->city_id !== $cities[$shift->id]) {
                $shift->update([
                    ScheduleShift::CITY_ID => $cities[$shift->id],
                ]);
            }
        });
    }

    public function getShift(int $driverId): ?ScheduleShift
    {
        $week = ScheduleWeek::current()->first();
        $gap = ScheduleGap::whereWeekId($week->id)
            ->whereWeekDay(now()->dayOfWeekIso)->first();

        return ScheduleShift::whereDriverId($driverId)
            ->whereGapId($gap->id)
            ->first();
    }

    /**
     * @param GetScheduleRequest $request
     * @param ScheduleWeekFilter $filter
     * @return ScheduleWeek|null
     */
    public function getWeek(GetScheduleRequest $request, ScheduleWeekFilter $filter): ?ScheduleWeek
    {
        if ($request->has('year') && $request->has('number')) {
            $week = ScheduleWeek::filter($filter)->with('gaps', 'gaps.shifts')->first();
        } else {
            $week = ScheduleWeek::current()->with('gaps', 'gaps.shifts')->first();
        }

        return $week;
    }

    /**
     * @param GetScheduleRequest $request
     * @return array
     */
    public function getDatesForWeekPicker(GetScheduleRequest $request): array
    {
        $firstWeek = $this->getBoundaryWeek();
        $lastWeek = $this->getBoundaryWeek(false);

        $currentYear = now()->year;
        $selectedYear = $request->input('year', $currentYear);

        $currentWeek = now()->weekOfYear;
        $selectedWeek = $request->input('number', $currentWeek);

        return [
            'startDate' => $this->getFormattedWeekStart(
                $firstWeek->year ?? $selectedYear,
                $firstWeek->number ?? $selectedWeek
            ),
            'endDate' => $this->getFormattedWeekEnd(
                $lastWeek->year ?? $selectedYear,
                $lastWeek->number ?? $selectedWeek
            ),
            'selectedDate' => $this->getFormattedWeekStart($selectedYear, $selectedWeek),
            'selectedYear' => $selectedYear,
            'selectedWeek' => $selectedWeek,
        ];
    }

    /**
     * @param int $year
     * @param int $weekNumber
     * @param string $format
     * @return string
     */
    protected function getFormattedWeekStart(int $year, int $weekNumber, string $format = 'm/d/Y'): string
    {
        return now()->setISODate($year, $weekNumber)
            ->startOfWeek()
            ->format($format);
    }

    /**
     * @param int $year
     * @param int $weekNumber
     * @param string $format
     * @return string
     */
    protected function getFormattedWeekEnd(int $year, int $weekNumber, string $format = 'm/d/Y'): string
    {
        return now()->setISODate($year, $weekNumber)
            ->endOfWeek()
            ->format($format);
    }

    public function getReportFileName(ScheduleWeek $week)
    {
        return 'report_' . $this->getFormattedWeekStart($week->year, $week->number, 'm-d-Y') . '_'
            . $this->getFormattedWeekEnd($week->year, $week->number, 'm-d-Y') . '.csv';
    }

    public function getWorkHours($scheduleShifts): int
    {
        return $scheduleShifts
            ->reduce(static function (int $hours,  ScheduleShift $shift) {
                $start = Carbon::createFromFormat('H:i:s', $shift->gap->start);
                $end = Carbon::createFromFormat('H:i:s', $shift->gap->end);

                if ($start->gt($end)) {
                    $end->addDay();
                }

                return $hours + $end->diffInHours($start);
            }, 0);
    }

    public function getWeekTitle(ScheduleWeek $week)
    {
        $weekStart = now()->setISODate($week->year, $week->number)->startOfWeek();
        $weekEnd = now()->setISODate($week->year, $week->number)->endOfWeek();

        if ($weekStart->year !== $weekEnd->year) {
            $startPartFormat = 'd F Y';
        } elseif ($weekStart->month !== $weekEnd->month) {
            $startPartFormat = 'd F';
        } else {
            $startPartFormat = 'd';
        }

        return $weekStart->format($startPartFormat)
            . ' - ' . $weekEnd->format('d F Y');
    }

    public function getShiftsForDriver(ScheduleWeek $week, Driver $driver)
    {
        return $week->shifts()
            ->whereDriverId($driver->id)
            ->get();
    }}
