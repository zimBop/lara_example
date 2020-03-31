<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VerificationCode;
use Faker\Generator as Faker;

$factory->define(
    VerificationCode::class,
    function (Faker $faker) {
        $code = implode(
            '',
            $faker->randomElements(
                range(0, 9),
                VerificationCode::CODE_LENGTH,
                true
            )
        );

        return [
            VerificationCode::CODE => $code,
            VerificationCode::EXPIRES_AT => now()->addMinutes(VerificationCode::LIFETIME_MINUTES),
        ];
    }
);
