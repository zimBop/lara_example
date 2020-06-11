<?php

namespace Tests\Feature\Driver;

use App\Models\City;
use App\Models\ScheduleGap;
use App\Models\ScheduleShift;
use App\Models\ScheduleWeek;
use App\Models\Shift;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    public function testIsShiftStarted()
    {
        $driver = $this->makeAuthDriver();

        $scheduleWeek = factory(ScheduleWeek::class)->create();
        $scheduleGap = factory(ScheduleGap::class)->create([
            ScheduleGap::WEEK_DAY => now()->dayOfWeekIso,
            ScheduleGap::WEEK_ID => $scheduleWeek->id,
        ]);

        $city = City::whereName('Hartford')->first();
        factory(ScheduleShift::class)->create([
            ScheduleShift::GAP_ID => $scheduleGap->id,
            ScheduleShift::DRIVER_ID => $driver->id,
            ScheduleShift::CITY_ID => $city->id,
        ]);

        $pendingShift = factory(Shift::class)->create([
            Shift::DRIVER_ID => $driver->id,
            Shift::CITY_ID => $city->id,
            Shift::STARTED_AT => now()->subDay(),
            Shift::FINISHED_AT => null,
        ]);

        $driver->refresh();

        $this->postJson(route('shift.start', ['driver' => $driver->id]), [
                'latitude' => 41.869390,
                'longitude' => -72.825148
            ])
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'data' => [
                    'id' => $driver->active_shift->id,
                 ]
             ]);

        // check pending shift finished
        $pendingShift->refresh();
        $this->assertTrue($pendingShift->finished_at !== null);
    }
}
