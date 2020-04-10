<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, static function (Faker $faker) {
    return [
        Client::FIRST_NAME => $faker->firstName,
        Client::LAST_NAME => $faker->lastName,
        Client::PHONE => \App\Services\ClientService::generatePhoneNumber(),
        Client::EMAIL => $faker->unique()->safeEmail,
        Client::EMAIL_VERIFIED_AT => $faker->dateTimeBetween('-6 days', '-5 days', 'America/New_York'),
        Client::PASSWORD => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        Client::BIRTHDAY => $faker->date(),
        Client::IS_ACTIVE => $faker->boolean(),
        Client::CREATED_AT => $faker->dateTimeBetween('-11 days', '-7 days', 'America/New_York'),
        Client::UPDATED_AT => $faker->dateTimeBetween('-4 days', 'now', 'America/New_York'),
    ];
});
