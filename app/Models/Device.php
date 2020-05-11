<?php

namespace App\Models;

use App\Constants\DeviceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Device
 *
 * @property int $id
 * @property int $type
 * @property string $token
 * @property int $model_id
 * @property string $model_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device ios()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

