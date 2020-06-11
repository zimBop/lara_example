<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ScheduleWeek;
use Faker\Generator as Faker;

$factory->define(ScheduleWeek::class, function (Faker $faker) {
    $now = now();

    return [
        ScheduleWeek::YEAR => $now->year,
        ScheduleWeek::NUMBER => $now->weekOfYear,
        ScheduleWeek::IS_TEMPLATE => false,
    ];
});
