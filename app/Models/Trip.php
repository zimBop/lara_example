<?php

namespace App\Models;

use App\Constants\TripStatuses;
use App\Logic\MetricConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Trip
 *
 * @property int $id
 * @property int $client_id
 * @property int $shift_id
 * @property int $status Trip Status
 * @property string $co2 Shows how much CO2 emission reduced in pounds
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
 * @property string|null $picked_up_at Timestamp of the moment when client picked up
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Shift $shift
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereCo2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereDriverDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereMessageForDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereOverviewPolyline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip wherePickedUpAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereTripDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereWaitDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip whereWaypoints($value)
 * @mixin \Eloquent
 * @property-read mixed $wait_duration_adjusted
 * @property-read mixed $created_at_timestamp
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trip archived()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Trip onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Trip withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Trip withoutTrashed()
 * @property-read mixed $is_free_trip
 * @property-read mixed $trip_duration_adjusted
 */
class Trip extends Model
{
    use SoftDeletes;

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
    public const WAIT_DURATION_ADJUSTED = 'wait_duration_adjusted';
    public const TRIP_DURATION = 'trip_duration';
    public const DISTANCE = 'distance';
    public const DRIVER_DISTANCE = 'driver_distance';
    public const CO2 = 'co2';
    public const MESSAGE_FOR_DRIVER = 'message_for_driver';
    public const PAYMENT_METHOD_ID = 'payment_method_id';
    public const PICKED_UP_AT = 'picked_up_at';
    public const CREATED_AT_TIMESTAMP = 'created_at_timestamp';
    public const UPDATED_AT_TIMESTAMP = 'updated_at_timestamp';
    public const IS_FREE_TRIP = 'is_free_trip';

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
        self::CO2 => 'float',
        self::STATUS => 'integer',
        self::PICKED_UP_AT => 'timestamp',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getWaitDurationAdjustedAttribute()
    {
        return $this->calculateAdjustedDuration(TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT, 'wait_duration');
    }

    public function getTripDurationAdjustedAttribute()
    {
        return $this->calculateAdjustedDuration(TripStatuses::UNRATED, 'trip_duration');
    }

    public function getCreatedAtTimestampAttribute()
    {
        return $this->created_at->timestamp;
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
        return $query->where(self::STATUS, '!=', TripStatuses::TRIP_ARCHIVED)
            ->where(self::CREATED_AT, '>', now()->subDay());
    }

    public function scopeArchived(Builder $query)
    {
        return $query->whereStatus(TripStatuses::TRIP_ARCHIVED);
    }

    public function getIsFreeTripAttribute()
    {
        return (bool) !$this->payment_method_id;
    }

    protected function calculateAdjustedDuration(int $status, $attribute)
    {
        if ($this->status >= $status) {
            return 0;
        }

        $duration = $this->{$attribute} - now()->diffInSeconds($this->created_at);

        return $duration > 0 ? $duration : 0 ;
    }
}
