<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    public const CLIENT_ID = 'client_id';
    public const CREATED_AT = 'created_at';
    public const TOKEN = 'token';

    protected $fillable = [
        self::CREATED_AT,
        self::TOKEN,
        self::CLIENT_ID,
    ];

    public $timestamps = false;

    public $primaryKey = 'client_id';

    protected $dates = [
        self::CREATED_AT,
    ];
}
