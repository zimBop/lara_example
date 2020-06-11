<?php

namespace App\Models;

use App\Scopes\IsNotTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filters\Filterable;

/**
 * App\Models\ScheduleWeek
 *
 * @property int $id
 * @property int $year
 * @property int $number
 * @property bool $is_template
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduleGap[] $gaps
 * @property-read int|null $gaps_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek current()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek template()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek whereIsTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek whereYear($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduleShift[] $shifts
 * @property-read int|null $shifts_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleWeek filter(\App\Filters\QueryFilter $filters)
 */
class ScheduleWeek extends Model
{
    use Filterable;

    public const YEAR = 'year';
    public const NUMBER = 'number';
    public const IS_TEMPLATE = 'is_template';

    protected $fillable = [
        self::YEAR,
        self::NUMBER,
        self::IS_TEMPLATE,
    ];

    protected static function booted()
    {
        static::addGlobalScope(new IsNotTemplate());
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeTemplate(Builder $query)
    {
        return $query->withoutGlobalScope(IsNotTemplate::class)->whereIsTemplate(true);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeCurrent(Builder $query)
    {
        return $query->whereNumber(now()->weekOfYear);
    }

    public function gaps()
    {
        return $this->hasMany(ScheduleGap::class, ScheduleGap::WEEK_ID)
            ->orderBy(ScheduleGap::WEEK_DAY)
            ->orderBy(ScheduleGap::START);
    }

    public function shifts()
    {
        return $this->hasManyThrough(
            ScheduleShift::class,
            ScheduleGap::class,
            ScheduleGap::WEEK_ID,
            ScheduleShift::GAP_ID
        );
    }
}
