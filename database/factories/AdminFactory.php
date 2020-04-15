<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Admin;
use Faker\Generator as Faker;

$factory->define(Admin::class, static function (Faker $faker) {
    return [
        Admin::NAME => $faker->name,
        Admin::EMAIL => $faker->unique()->safeEmail,
        Admin::PASSWORD => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        Admin::CREATED_AT => $faker->dateTimeBetween('-11 days', '-7 days', 'America/New_York'),
        Admin::UPDATED_AT => $faker->dateTimeBetween('-4 days', 'now', 'America/New_York'),
    ];
});
