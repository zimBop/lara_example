<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    public const CODE = 'code';
    public const CLIENT_ID = 'client_id';
    public const EXPIRES_AT = 'expires_at';
    public const VERIFIED_AT = 'verified_at';

    public const LIFETIME_MINUTES = 15;
    public const CODE_LENGTH = 4;
    public const DELAY_MINUTES = 1;

    protected $dates = [
        self::EXPIRES_AT,
    ];

    protected $fillable = [
        self::CODE,
    ];

    public function getIsExpiredAttribute()
    {
        return now()->gte($this->expires_at);
    }
}
