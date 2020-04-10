<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * App\Models\Client
 *
 * @property int $id
 * @property string $phone
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $birthday
 * @property string|null $password
 * @property string|null $email
 * @property string|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $customer_id Stripe customer id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read mixed $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\PasswordResetToken $passwordResetToken
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\VerificationCode $verificationCode
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
    public const CUSTOMER_ID = 'customer_id';

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
        self::CUSTOMER_ID,
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

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
