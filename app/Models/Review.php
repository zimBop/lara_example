<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
