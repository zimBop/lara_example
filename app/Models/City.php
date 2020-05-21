<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
