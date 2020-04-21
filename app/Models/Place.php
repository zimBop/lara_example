<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    public const CLIENT_ID = 'client_id';
    public const DESCRIPTION = 'description';
    public const ID = 'id';
    public const NAME = 'name';
    public const PLACE_ID = 'place_id';

    protected $fillable = [
        self::CLIENT_ID,
        self::DESCRIPTION,
        self::NAME,
        self::PLACE_ID,
    ];

    public function post()
    {
        return $this->belongsTo(Client::class);
    }
}
