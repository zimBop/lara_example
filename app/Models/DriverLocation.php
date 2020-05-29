<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class DriverLocation extends Model
{
    public const SHIFT_ID = 'shift_id';
    public const LOCATION = 'location';

    protected $fillable = [
        self::SHIFT_ID,
        self::LOCATION,
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getDriverAttribute()
    {
        return $this->shift->driver;
    }

    public function scopeWithCoordinates(Builder $builder)
    {
        return $builder->select(
            'driver_locations.*',
            DB::raw('ST_X(location) as longitude'),
            DB::raw('ST_Y(location) as latitude')
        );
    }
}
