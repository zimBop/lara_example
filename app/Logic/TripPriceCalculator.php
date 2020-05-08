<?php

namespace App\Logic;

use App\Models\TripOrder;

class TripPriceCalculator
{
    // All prices in cents
    protected const BOOKING_FEE = 300;
    protected const MINIMUM_FARE = 700;
    protected const PER_MINUTE = 28;
    protected const PER_MILE = 88;
    protected const DRIVER_BASE_FARE = 180;
    protected const LONG_PICKUP_FEE = 500;

    protected const LONG_PICKUP_SECONDS = 30 * 60;

    /**
     * @param array $tripData
     * @return mixed
     */
    public static function calculatePrice(array $tripData)
    {
        $clientPartPrice = self::calculateClientPartPrice(
            $tripData[TripOrder::TRIP_DURATION],
            $tripData[TripOrder::DISTANCE]
        );

        $driverPartPrice = self::calculateDriverPartPrice(
            $tripData[TripOrder::WAIT_DURATION],
            $tripData[TripOrder::DRIVER_DISTANCE]
        );

        $predictedPrice = round($clientPartPrice + $driverPartPrice);

        return max($predictedPrice, self::MINIMUM_FARE);
    }

    /**
     * @param $duration int Trip duration in seconds
     * @param $distance int Trip distance in meters
     * @return float|int
     */
    protected static function calculateClientPartPrice(int $duration, int $distance)
    {
        $distanceInMiles = MetricConverter::metersToMiles($distance);

        return self::BOOKING_FEE + self::PER_MINUTE * ($duration / 60) + self::PER_MILE * $distanceInMiles;
    }

    /**
     * @param $duration int Trip duration in seconds
     * @param $distance int Trip distance in meters
     * @return float|int
     */
    protected static function calculateDriverPartPrice(int $duration, int $distance)
    {
        $longPickupFee = $duration > self::LONG_PICKUP_SECONDS ? self::LONG_PICKUP_FEE : 0;
        $distanceInMiles = MetricConverter::metersToMiles($distance);

        return self::DRIVER_BASE_FARE + $longPickupFee + self::PER_MINUTE * ($duration / 60)
            + self::PER_MILE * $distanceInMiles;
    }
}
