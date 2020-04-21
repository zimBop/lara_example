<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Place;
use Faker\Generator as Faker;

$factory->define(Place::class, function (Faker $faker) {
    return [
        Place::NAME => $faker->word,
        Place::DESCRIPTION => $faker->address,
        Place::PLACE_ID => $faker->word,
    ];
});
