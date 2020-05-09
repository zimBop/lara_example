<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
