<?php

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\ScheduleWeek;
use App\Models\ScheduleGap;
use App\Models\ScheduleShift;

class ScheduleWeekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $weekTemplate = ScheduleWeek::with(['gaps', 'gaps.shifts'])->template()->first();

        if (!$weekTemplate) {
            $weekTemplate = $this->createWeekTemplate();
        }

        $currentWeekNumber = now()->weekOfYear;

        foreach ([$currentWeekNumber, $currentWeekNumber + 1] as $weekNumber) {
            if (!$this->isWeekExists($weekNumber)) {
                $this->copyWeekFromTemplate($weekNumber, $weekTemplate);
            }
        }
    }

    protected function createWeekTemplate(): ScheduleWeek
    {
        $week = factory(ScheduleWeek::class)->create([ScheduleWeek::IS_TEMPLATE => true]);

        collect(range(1, 7))
            ->reduce(function ($weekGaps, $weekDay) use ($week) {
                return $weekGaps->merge(
                    $this->createGapsForWeekDay($week, $weekDay)
                );
            }, collect([]))
            ->each(function ($gap) {
                $this->createShiftsForGap($gap);
            });

        $week->load(['gaps', 'gaps.shifts']);

        return $week;
    }

    protected function createGapsForWeekDay(ScheduleWeek $week, int $weekDay)
    {
        return collect([
            ['start' => 5, 'end' => 14],
            ['start' => 16, 'end' => 26],
        ])->reduce(function ($gaps, $bounds) use ($week, $weekDay) {
            return $gaps->prepend(
                factory(ScheduleGap::class)->create([
                    ScheduleGap::WEEK_ID => $week->id,
                    ScheduleGap::WEEK_DAY => $weekDay,
                    ScheduleGap::START => now()->startOfDay()->addHours($bounds['start']),
                    ScheduleGap::END => now()->startOfDay()->addHours($bounds['end']),
                ])
            );
        }, collect([]));
    }

    protected function createShiftsForGap(ScheduleGap $gap): void
    {
        Vehicle::all()
            ->each(
                function ($vehicle) use ($gap) {
                    factory(ScheduleShift::class)->create(
                        [
                            ScheduleShift::GAP_ID => $gap->id,
                            ScheduleShift::VEHICLE_ID => $vehicle->id,
                            ScheduleShift::DRIVER_ID => null,
                            ScheduleShift::CITY_ID => null,
                        ]
                    );
                }
            );
    }

    protected function isWeekExists(int $weekNumber)
    {
        return ScheduleWeek::whereNumber($weekNumber)
            ->exists();
    }

    protected function copyWeekFromTemplate(int $weekNumber, ScheduleWeek $weekTemplate): void
    {
        $week = $weekTemplate->replicate()
            ->fill([
                ScheduleWeek::NUMBER => $weekNumber,
                ScheduleWeek::YEAR => now()->year,
                ScheduleWeek::IS_TEMPLATE => false,
            ]);

        $week->push();

        $weekTemplate->gaps->each(
            function ($gapTemplate) use ($week) {
                $gapAttributes = $gapTemplate->getAttributes();
                unset($gapAttributes['id'], $gapAttributes['week']);
                $gap = $week->gaps()->create($gapAttributes);

                $gapTemplate->shifts->each(function ($shiftTemplate) use ($gap) {
                    $shiftAttributes = $shiftTemplate->getAttributes();
                    unset($shiftAttributes['id']);
                    $gap->shifts()->create($shiftAttributes);
                });
            }
        );
    }
}
