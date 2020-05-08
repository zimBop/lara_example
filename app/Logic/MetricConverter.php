<?php

namespace App\Logic;

class MetricConverter
{
    /**
     * @param int distance in meters
     * @return float distance in miles
     */
    public static function metersToMiles(int $meters): float
    {
        return $meters * 0.000621371192;
    }

    /**
     * @param int mass in grams
     * @return float mass in pounds
     */
    public static function gramsToPounds(int $grams): float
    {
        return $grams * 0.0022046;
    }
}
