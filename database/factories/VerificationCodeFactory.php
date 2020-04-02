<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VerificationCode;
use Faker\Generator as Faker;
use App\Services\VerificationCodeService;

$factory->define(
    VerificationCode::class,
    function (Faker $faker) {
        $code = VerificationCodeService::generate();

        return [
            VerificationCode::CODE => $code,
            VerificationCode::EXPIRES_AT => now()->addMinutes(config('app.verification_code.lifetime')),
        ];
    }
);
