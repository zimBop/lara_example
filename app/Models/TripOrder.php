<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TripOrder
 *
 * @property int $id
 * @property int|null $client_id
 * @property int|null $shift_id
 * @property int $status Trip Order Status
 * @property array $origin Origin Google Place info
 * @property array $destination Destination Google Place info
 * @property array|null $waypoints Google Place or Reverse Geocoding info for waypoints
 * @property array $overview_polyline Contains a single points object that holds an encoded polyline representation of the route
 * @property int $price Trip price in cents
 * @property int $wait_duration Driver waiting time in seconds
 * @property int $trip_duration Trip duration in seconds
 * @property int $distance Trip distance in meters
 * @property int $driver_distance Distance in meters between diver' location and origin
 * @property string|null $message_for_driver
 * @property string|null $payment_method_id Stripe payment method id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereDriverDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereMessageForDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereOverviewPolyline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereTripDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereWaitDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TripOrder whereWaypoints($value)
 * @mixin \Eloquent
 */
class TripOrder extends Model
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
    public const MESSAGE_FOR_DRIVER = 'message_for_driver';
    public const PAYMENT_METHOD_ID = 'payment_method_id';

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
        self::MESSAGE_FOR_DRIVER,
        self::PAYMENT_METHOD_ID,
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
}
