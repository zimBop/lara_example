<?php

namespace App\Models;

use App\Constants\DeviceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public const TYPE = 'type';
    public const TOKEN = 'token';

    protected $fillable = [
        self::TYPE,
        self::TOKEN,
    ];

    public function model()
    {
        return $this->morphTo();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeIos(Builder $query)
    {
        return $query->where(static::TYPE, DeviceType::IOS);
    }
}

