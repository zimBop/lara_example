<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Client extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public const ID = 'id';
    public const PHONE = 'phone';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const BIRTHDAY = 'birthday';
    public const EMAIL = 'email';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PASSWORD = 'password';

    protected $fillable = [
        self::BIRTHDAY,
        self::EMAIL,
        self::EMAIL_VERIFIED_AT,
        self::PASSWORD,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::PHONE,
    ];

    protected $hidden = [
        self::PASSWORD,
        self::EMAIL_VERIFIED_AT,
    ];

    /**
     * This method used in Laravel Passport validation
     */
    public function findForPassport($phone)
    {
        return $this->where(self::PHONE, $phone)->first();
    }

    /**
     * This method used in Laravel Passport validation
     */
    public function validateForPassportPasswordGrant($smsCode)
    {
        if ($this->verificationCode && $this->verificationCode->expires_at->gt(now())) {
            return $smsCode == $this->verificationCode->code;
        }

        return false;
    }

    public function verificationCode()
    {
        return $this->hasOne(VerificationCode::class);
    }

    public function passwordResetToken()
    {
        return $this->hasOne(PasswordResetToken::class);
    }

    public function isRegistrationCompleted(): bool
    {
        return $this->first_name && $this->last_name && $this->password;
    }
}
