<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use SMartins\PassportMultiauth\HasMultiAuthApiTokens;

class Driver extends Authenticatable
{
    use HasMultiAuthApiTokens, Notifiable;

    public const ID = 'id';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';

    protected $fillable = [
        self::EMAIL,
        self::PASSWORD,
        self::FIRST_NAME,
        self::LAST_NAME,
    ];
}
