<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Vehicle;
use App\Services\VehicleService;
use Faker\Generator as Faker;

$factory->define(Vehicle::class, static function (Faker $faker) {
    $brand_id = VehicleService::generateRandomBrand();
    $model_id = VehicleService::generateRandomModel($brand_id);
    return [
        Vehicle::LICENSE_PLATE => VehicleService::generateRandomLicensePlate(),
        Vehicle::BRAND_ID => $brand_id,
        Vehicle::MODEL_ID => $model_id,
        Vehicle::COLOR_ID => VehicleService::generateRandomColor(),
        Vehicle::STATUS_ID => VehicleService::generateRandomStatus(),
        Vehicle::CREATED_AT => $faker->dateTimeBetween('-5 days', '-3 days', 'America/New_York'),
        Vehicle::UPDATED_AT => $faker->dateTimeBetween('-2 days', 'now', 'America/New_York'),
    ];
});
