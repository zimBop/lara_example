<?php

namespace App\Models;

use App\Constants\TripStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Shift
 *
 * @property int $id
 * @property int|null $driver_id
 * @property int|null $vehicle_id
 * @property string $started_at
 * @property string|null $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Driver|null $driver
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trip[] $trips
 * @property-read int|null $trips_count
 * @property-read \App\Models\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read mixed $active_trip
 * @property-read \App\Models\DriverLocation $driver_location
 * @property int|null $city_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereCityId($value)
 * @property int|null $washed_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TripOrder[] $trip_orders
 * @property-read int|null $trip_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift pending()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shift whereWashedAt($value)
 */
class Shift extends Model
{
    public const ID = 'id';
    public const DRIVER_ID = 'driver_id';
    public const VEHICLE_ID = 'vehicle_id';
    public const CITY_ID = 'city_id';
    public const SCHEDULE_SHIFT_ID = 'schedule_shift_id';
    public const STARTED_AT = 'started_at';
    public const FINISHED_AT = 'finished_at';
    public const WASHED_AT = 'washed_at';

    protected $fillable = [
        self::DRIVER_ID,
        self::VEHICLE_ID,
        self::CITY_ID,
        self::SCHEDULE_SHIFT_ID,
        self::STARTED_AT,
        self::FINISHED_AT,
        self::WASHED_AT,
    ];

    protected $casts = [
        self::WASHED_AT => 'timestamp',
        self::STARTED_AT => 'datetime',
    ];

    public function getActiveTripAttribute()
    {
        return $this->trips()
            ->active()
            ->where(Trip::STATUS, '<', TripStatuses::UNRATED)
            ->first();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->whereNull(self::FINISHED_AT)
            ->where(self::STARTED_AT, '>', now()->subDay())
            ->orderByDesc(self::STARTED_AT);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePending(Builder $query)
    {
        $activeShift = $this->active()->first();

        return $query->whereNull(self::FINISHED_AT)
            ->when($activeShift, static function (Builder $query, $activeShift) {
                return $query->whereNotIn(self::ID, [$activeShift->id]);
            });
    }

    public function schedule_shift()
    {
        return $this->belongsTo(ScheduleShift::class);
    }

    // TODO remove 'driver' and 'vehicle' relations to ScheduleShift
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function trip_orders()
    {
        return $this->belongsToMany(TripOrder::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function driver_location()
    {
        return $this->hasOne(DriverLocation::class);
    }
}
