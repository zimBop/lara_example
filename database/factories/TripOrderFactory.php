<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TripOrder;
use Faker\Generator as Faker;

$factory->define(TripOrder::class, function (Faker $faker) {
    return [
        TripOrder::STATUS => $faker->randomElement(\App\Constants\TripOrderStatuses::getList()),
        TripOrder::ORIGIN => 'place_id:ChIJTYPjdjRV5okRIn-weMnRXXw',
        TripOrder::DESTINATION => 'place_id:ChIJzeZ1iHyr54kR4tA85sx6RGM',
        TripOrder::WAYPOINTS => [
            'place_id:ChIJk1_Sr3Gr54kRcACgP1AcAgM',
            '41.790789,-72.746783',
        ],
        TripOrder::OVERVIEW_POLYLINE => [
            'points' => 'g_i~FdjwzLNLLDjDZ~@LdBZbCd@lA\tAp@xAv@h@VzAn@`HfCb@Lj@LxBJBbBJbAXzAdB`Kb@pCXrCT~EJrCtM|CfFnAZF\@\Ab@G@~ABhAF|@JdA`AbGVbBTrB@^JfGNxMJpHh@xd@^vc@`@xg@GbHOjIB`ADV\nA\f@ZZRJRFz@PP?bGbAvFz@jARpAHfD?rBInDOdCKb@?N@x@L|@\rHfDfAf@TZzBfAPJdARjBDt@CNMl@AbCC|@GjAQvFmAlCu@xAk@t@a@zKwFpAo@p@]GKI[c@iC]oCMcBnCkB`M{IjAo@x@]bA]~@S~@I~@G@`Ar@xIj@hI{@FyB`@}Dz@yGnAaG~@w@XSHGKI[c@iC]oCMcBnCkB`M{IjAo@x@]bA]~@S~@I~@G@`Ar@xIj@hIpA@tAJ|AHz@Jv@NpFrAlD`AhDx@',
        ],
        TripOrder::PRICE => $faker->randomNumber(4),
        TripOrder::WAIT_DURATION => $faker->randomNumber(4),
        TripOrder::TRIP_DURATION => $faker->randomNumber(4),
        TripOrder::DISTANCE => $faker->randomNumber(4),
        TripOrder::DRIVER_DISTANCE => $faker->randomNumber(4),
    ];
});
