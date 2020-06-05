<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ScheduleGap
 *
 * @property int $id
 * @property int $week_day
 * @property string $start
 * @property string $end
 * @property int $week_id Schedule week ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduleShift[] $shifts
 * @property-read int|null $shifts_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap whereWeekDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScheduleGap whereWeekId($value)
 * @mixin \Eloquent
 */
class ScheduleGap extends Model
{
    public const WEEK_ID = 'week_id';
    public const WEEK_DAY = 'week_day';
    public const START = 'start';
    public const END = 'end';

    protected $fillable = [
        self::WEEK_ID,
        self::WEEK_DAY,
        self::START,
        self::END,
    ];

    public function getStartFormattedAttribute()
    {
        return Carbon::createFromFormat('H:i:s', $this->start)->format('g A');
    }

    public function getEndFormattedAttribute()
    {
        return Carbon::createFromFormat('H:i:s', $this->end)->format('g A');
    }

    public function shifts()
    {
        return $this->hasMany(ScheduleShift::class, ScheduleShift::GAP_ID);
    }
}
