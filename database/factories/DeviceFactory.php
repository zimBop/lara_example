<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Device;
use Faker\Generator as Faker;

$factory->define(Device::class, function (Faker $faker) {
    return [
        Device::TYPE => $faker->randomElement(\App\Constants\DeviceType::getList()),
        Device::TOKEN => $faker->word,
    ];
});
