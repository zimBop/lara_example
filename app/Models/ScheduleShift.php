<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ScheduleShift
 *
 * @property int $id
 * @property int $gap_id Schedule shift ID
 * @property int $vehicle_id
 * @property int|null $city_id
 * @property int|null $driver_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift whereGapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleShift whereVehicleId($value)
 * @mixin \Eloquent
 */
class ScheduleShift extends Model
{
    public const GAP_ID = 'gap_id';
    public const DRIVER_ID = 'driver_id';
    public const VEHICLE_ID = 'vehicle_id';
    public const CITY_ID = 'city_id';

    protected $fillable = [
        self::GAP_ID,
        self::DRIVER_ID,
        self::VEHICLE_ID,
        self::CITY_ID,
    ];

    public function gap()
    {
        return $this->belongsTo(ScheduleGap::class, self::GAP_ID);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
