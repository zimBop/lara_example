<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ScheduleGap;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(ScheduleGap::class, function (Faker $faker) {
    $weekDay = $faker->numberBetween(1, 7);
    $startHour = $faker->numberBetween(1, 23);
    $start = Carbon::createFromTime($startHour);
    $endHour = $faker->numberBetween($startHour + 1, 24);
    $end = Carbon::createFromTime($endHour);

    return [
        ScheduleGap::WEEK_DAY => $weekDay,
        ScheduleGap::START => $start,
        ScheduleGap::END => $end,
    ];
});
