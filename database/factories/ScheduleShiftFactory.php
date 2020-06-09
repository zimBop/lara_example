<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\City;
use App\Models\ScheduleGap;
use App\Models\ScheduleShift;
use Faker\Generator as Faker;

$factory->define(ScheduleShift::class, function (Faker $faker) {
    $driver = Driver::first() ?: factory(Driver::class)->create();
    $vehicle = Vehicle::first() ?: factory(Vehicle::class)->create();
    $city = City::first();
    $scheduleGap = ScheduleGap::first() ?: factory(ScheduleGap::class)->create();

    return [
        ScheduleShift::GAP_ID => $scheduleGap->id,
        ScheduleShift::CITY_ID => $city->id,
        ScheduleShift::DRIVER_ID => $driver->id,
        ScheduleShift::VEHICLE_ID => $vehicle->id,
    ];
});
