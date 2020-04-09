<?php

namespace App\Services;
use App\Constants\VehicleConstants;

class VehicleService
{
    public static function generateRandomLicensePlate(): string
    {
        $chars = array_merge(range(0,9), range('A', 'Z'));
        return substr(str_shuffle(implode($chars)), 0, rand(4,8));
    }

    public static function generateRandomBrand(): int
    {
       return array_rand(VehicleConstants::BRANDS);
    }

    public static function generateRandomModel($brand_id): int
    {
        return array_rand(VehicleConstants::BRANDS[$brand_id]['models']);
    }

    public static function generateRandomStatus(): int
    {
        return array_rand(VehicleConstants::STATUSES);
    }

    public static function generateRandomColor(): int
    {
        return array_rand(VehicleConstants::COLORS);
    }
}
