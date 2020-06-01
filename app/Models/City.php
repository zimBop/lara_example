<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class City extends Model
{
    public const NAME = 'name';
    public const POLYGON = 'polygon';
    public const CENTER = 'center';

    protected $fillable = [
        self::NAME,
        self::POLYGON,
        self::CENTER,
    ];

    public function scopeWithCoordinates(Builder $builder)
    {
        return $builder->select(
            'cities.*',
            DB::raw('ST_X(center) as longitude'),
            DB::raw('ST_Y(center) as latitude')
        );
    }
}
