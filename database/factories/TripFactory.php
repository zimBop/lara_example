<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Trip;
use Faker\Generator as Faker;

$factory->define(Trip::class, function (Faker $faker) {
    $client = factory(\App\Models\Client::class)->create();
    $distance = $faker->randomNumber(4);

    return [
        Trip::CLIENT_ID => $client->id,
        Trip::STATUS => $faker->randomElement(\App\Constants\TripStatuses::getList()),
        Trip::ORIGIN => [
            'id' => 'place_id:ChIJTYPjdjRV5okRIn-weMnRXXw',
            'label' => 'test origin label',
            'coordinates' => [
                'lat' => 41.8304407,
                'lng' => -72.7057949
            ]
        ],
        Trip::DESTINATION => [
            'id' => 'place_id:ChIJzeZ1iHyr54kR4tA85sx6RGM',
            'label' => 'test destination label',
            'coordinates' => [
                'lat' => 41.7879616,
                'lng' => -72.74785419999999
            ]
        ],
        Trip::WAYPOINTS => [
            [
                'id' => 'place_id:ChIJk1_Sr3Gr54kRcACgP1AcAgM',
                'label' => 'test waypoint1 label',
                'coordinates' => [
                    'lat' => 41.7962095,
                    'lng' => -72.7474948
                ]
            ],
            [
                'id' => '41.790789,-72.746783',
                'label' => 'test waypoint1 label',
                'coordinates' => [
                    'lat' => 41.7907986,
                    'lng' => -72.7468404
                ]
            ],
        ],
        Trip::OVERVIEW_POLYLINE => [
            'points' => 'g_i~FdjwzLNLLDjDZ~@LdBZbCd@lA\tAp@xAv@h@VzAn@`HfCb@Lj@LxBJBbBJbAXzAdB`Kb@pCXrCT~EJrCtM|CfFnAZF\@\Ab@G@~ABhAF|@JdA`AbGVbBTrB@^JfGNxMJpHh@xd@^vc@`@xg@GbHOjIB`ADV\nA\f@ZZRJRFz@PP?bGbAvFz@jARpAHfD?rBInDOdCKb@?N@x@L|@\rHfDfAf@TZzBfAPJdARjBDt@CNMl@AbCC|@GjAQvFmAlCu@xAk@t@a@zKwFpAo@p@]GKI[c@iC]oCMcBnCkB`M{IjAo@x@]bA]~@S~@I~@G@`Ar@xIj@hI{@FyB`@}Dz@yGnAaG~@w@XSHGKI[c@iC]oCMcBnCkB`M{IjAo@x@]bA]~@S~@I~@G@`Ar@xIj@hIpA@tAJ|AHz@Jv@NpFrAlD`AhDx@',
        ],
        Trip::PRICE => $faker->randomNumber(4),
        Trip::WAIT_DURATION => $faker->randomNumber(4),
        Trip::TRIP_DURATION => $faker->randomNumber(4),
        Trip::DISTANCE => $distance,
        Trip::CO2 => $distance,
        Trip::DRIVER_DISTANCE => $faker->randomNumber(4),
    ];
});
