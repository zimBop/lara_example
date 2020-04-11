<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Driver;
use Faker\Generator as Faker;

$factory->define(Driver::class, function (Faker $faker) {
    return [
        Driver::FIRST_NAME => $faker->firstName,
        Driver::LAST_NAME => $faker->lastName,
        Driver::EMAIL => $faker->unique()->safeEmail,
        Driver::PASSWORD => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    ];
});
