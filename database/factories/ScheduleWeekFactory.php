<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ScheduleWeek;
use Faker\Generator as Faker;

$factory->define(ScheduleWeek::class, function (Faker $faker) {
    $now = now();

    return [
        ScheduleWeek::YEAR => $now->year,
        ScheduleWeek::MONTH => $now->month,
        ScheduleWeek::NUMBER => $now->weekNumberInMonth,
        ScheduleWeek::IS_TEMPLATE => false,
    ];
});
