<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Review
 *
 * @property int $id
 * @property int|null $trip_id
 * @property int $rating
 * @property string|null $comment
 * @property int $model_id
 * @property string $model_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Review extends Model
{
    public const TRIP_ID = 'trip_id';
    public const RATING = 'rating';
    public const COMMENT = 'comment';

    protected $fillable = [
        self::TRIP_ID,
        self::RATING,
        self::COMMENT,
    ];
}
