<?php

namespace App\Services;

use App\Constants\VehicleConstants;

class VehicleService
{

    public static function getBrands()
    {
        return collect(VehicleConstants::BRANDS)->map(static function ($item, $key) {
            $item['id'] = $key;
            return (object) $item;
        });
    }

    public static function getModels($brand_id)
    {
        return collect(VehicleConstants::BRANDS[$brand_id]['models'])->map(static function ($item, $key) {
            $return['name'] = $item;
            $return['id'] = $key;
            return (object) $return;
        });
    }

    public static function getColors()
    {
        return collect(VehicleConstants::COLORS)->map(static function ($item, $key) {
            $item['id'] = $key;
            return (object) $item;
        });
    }

    public static function getStatuses()
    {
        return collect(VehicleConstants::STATUSES)->map(static function ($item, $key) {
            $item['id'] = $key;
            return (object) $item;
        });
    }

    public static function generateRandomLicensePlate(): string
    {
        $chars = array_merge(range(0, 9), range('A', 'Z'));
        return substr(str_shuffle(implode($chars)), 0, rand(4, 8));
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
