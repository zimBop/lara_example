<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Shift;
use Faker\Generator as Faker;

$factory->define(Shift::class, function (Faker $faker) {
    $driver = factory(\App\Models\Driver::class)->create();
    $vehicle = factory(\App\Models\Vehicle::class)->create();

    return [
        Shift::DRIVER_ID => $driver->id,
        Shift::VEHICLE_ID => $vehicle->id,
        Shift::STARTED_AT => now(),
        Shift::FINISHED_AT => null,
        Shift::WASHED_AT => now(),
    ];
});
