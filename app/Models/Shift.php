<?php

namespace App\Models;

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
 */
class Shift extends Model
{
    public const ID = 'id';
    public const DRIVER_ID = 'driver_id';
    public const VEHICLE_ID = 'vehicle_id';
    public const STARTED_AT = 'started_at';
    public const FINISHED_AT = 'finished_at';

    protected $fillable = [
        self::DRIVER_ID,
        self::VEHICLE_ID,
        self::STARTED_AT,
        self::FINISHED_AT,
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->whereNull(self::FINISHED_AT)
            ->where(self::STARTED_AT, '>', now()->subDay());
    }
}
