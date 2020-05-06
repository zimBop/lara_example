<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripOrder extends Model
{
    public const ID = 'id';
    public const CLIENT_ID = 'client_id';
    public const SHIFT_ID = 'shift_id';
    public const STATUS = 'status';
    public const ORIGIN = 'origin';
    public const DESTINATION = 'destination';
    public const WAYPOINTS = 'waypoints';
    public const COORDINATES = 'coordinates';
    public const OVERVIEW_POLYLINE = 'overview_polyline';
    public const PRICE = 'price';
    public const WAIT_DURATION = 'wait_duration';
    public const TRIP_DURATION = 'trip_duration';
    public const DISTANCE = 'distance';
    public const DRIVER_DISTANCE = 'driver_distance';
    public const MESSAGE_FOR_DRIVER = 'message_for_driver';
    public const PAYMENT_METHOD_ID = 'payment_method_id';

    protected $fillable = [
        self::CLIENT_ID,
        self::SHIFT_ID,
        self::STATUS,
        self::ORIGIN,
        self::DESTINATION,
        self::WAYPOINTS,
        self::COORDINATES,
        self::OVERVIEW_POLYLINE,
        self::PRICE,
        self::WAIT_DURATION,
        self::TRIP_DURATION,
        self::DISTANCE,
        self::DRIVER_DISTANCE,
        self::MESSAGE_FOR_DRIVER,
        self::PAYMENT_METHOD_ID,
    ];

    protected $casts = [
        self::ORIGIN => 'array',
        self::DESTINATION => 'array',
        self::WAYPOINTS => 'array',
        self::COORDINATES => 'array',
        self::OVERVIEW_POLYLINE => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
