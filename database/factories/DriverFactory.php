<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Driver;
use Faker\Generator as Faker;

$factory->define(Driver::class, static function (Faker $faker) {
    return [
        Driver::FIRST_NAME => $faker->firstName,
        Driver::LAST_NAME => $faker->lastName,
        Driver::EMAIL => $faker->unique()->safeEmail,
        Driver::PASSWORD => 'password', // will be hashed in password setter
        Driver::IS_ACTIVE => $faker->boolean(80),
        Driver::CREATED_AT => $faker->dateTimeBetween('-15 days', '-9 days', 'America/New_York'),
        Driver::UPDATED_AT => $faker->dateTimeBetween('-8 days', 'now', 'America/New_York'),
    ];
});
