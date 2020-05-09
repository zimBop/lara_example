<?php

namespace App\Models;

use App\Constants\TripStatuses;
use App\Logic\MetricConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Trip extends Model
{
    public const ID = 'id';
    public const CLIENT_ID = 'client_id';
    public const SHIFT_ID = 'shift_id';
    public const STATUS = 'status';
    public const ORIGIN = 'origin';
    public const DESTINATION = 'destination';
    public const WAYPOINTS = 'waypoints';
    public const OVERVIEW_POLYLINE = 'overview_polyline';
    public const PRICE = 'price';
    public const WAIT_DURATION = 'wait_duration';
    public const TRIP_DURATION = 'trip_duration';
    public const DISTANCE = 'distance';
    public const DRIVER_DISTANCE = 'driver_distance';
    public const CO2 = 'co2';
    public const MESSAGE_FOR_DRIVER = 'message_for_driver';
    public const PAYMENT_METHOD_ID = 'payment_method_id';
    public const PICKED_UP_AT = 'picked_up_at';

    protected $fillable = [
        self::CLIENT_ID,
        self::SHIFT_ID,
        self::STATUS,
        self::ORIGIN,
        self::DESTINATION,
        self::WAYPOINTS,
        self::OVERVIEW_POLYLINE,
        self::PRICE,
        self::WAIT_DURATION,
        self::TRIP_DURATION,
        self::DISTANCE,
        self::DRIVER_DISTANCE,
        self::CO2,
        self::MESSAGE_FOR_DRIVER,
        self::PAYMENT_METHOD_ID,
        self::PICKED_UP_AT,
    ];

    protected $casts = [
        self::ORIGIN => 'array',
        self::DESTINATION => 'array',
        self::WAYPOINTS => 'array',
        self::OVERVIEW_POLYLINE => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function setCo2Attribute($meters)
    {
        $miles = MetricConverter::metersToMiles($meters);
        $grams = 411 * $miles;

        return $this->attributes['co2'] = round(MetricConverter::gramsToPounds($grams), 4);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where(self::STATUS, '<', TripStatuses::TRIP_ARCHIVED)
            ->where(self::CREATED_AT, '>', now()->subDay());
    }
}
