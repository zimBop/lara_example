<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use SMartins\PassportMultiauth\HasMultiAuthApiTokens;

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
 * @property bool $is_active
 * @property-read mixed $age
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereIsActive($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Place[] $places
 * @property-read int|null $places_count
 * @property-read \App\Models\TripOrder $tripOrder
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Device[] $devices
 * @property-read int|null $devices_count
 * @property-read mixed $active_trip
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trip[] $trips
 * @property-read int|null $trips_count
 * @property float|null $rating
 * @property float|null $co2_sum Shows how much CO2 emission reduced in pounds for all time
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
 * @property-read int|null $reviews_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereCo2Sum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereRating($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invitation[] $invitations
 * @property-read int|null $invitations_number
 * @property int $free_trips
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client whereFreeTrips($value)
 * @property-read int|null $invitations_count
 */
class Client extends Authenticatable
{
    use HasMultiAuthApiTokens, Notifiable, CanReceiveIosPush, CanBeRated;

    public const ID = 'id';
    public const PHONE = 'phone';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const BIRTHDAY = 'birthday';
    public const EMAIL = 'email';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PASSWORD = 'password';
    public const IS_ACTIVE = 'is_active';
    public const CUSTOMER_ID = 'customer_id';
    public const RATING = 'rating';
    public const CO2_SUM = 'co2_sum';
    public const FREE_TRIPS = 'free_trips';

    protected $fillable = [
        self::BIRTHDAY,
        self::EMAIL,
        self::EMAIL_VERIFIED_AT,
        self::PASSWORD,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::PHONE,
        self::IS_ACTIVE,
        self::RATING,
        self::CO2_SUM,
    ];

    protected $hidden = [
        self::PASSWORD,
        self::EMAIL_VERIFIED_AT,
        self::CUSTOMER_ID,
    ];

    protected $casts = [
        self::IS_ACTIVE => 'boolean',
        self::BIRTHDAY => 'date',
        self::CO2_SUM => 'float',
        self::FREE_TRIPS => 'integer',
        self::RATING => 'float',
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
        if (app()->env !== 'production' && $smsCode == config('app.verification_code.test')) {
            return true;
        }

        if ($this->verificationCode && $this->verificationCode->expires_at->gt(now())) {
            return $smsCode == $this->verificationCode->code;
        }

        return false;
    }

    public function isRegistrationCompleted(): bool
    {
        return $this->first_name && $this->last_name && $this->password;
    }

    public function getFullNameAttribute(): string
    {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }

    public function getAgeAttribute(): int
    {
        // Consider refactoring here with Carbon
        return (new \DateTime())->diff(new \DateTime($this->birthday))->y;
    }

    public function getActiveTripAttribute()
    {
        return $this->trips()->active()->first();
    }

    public function getInvitationsNumberAttribute()
    {
        $number = config('app.invites.number') - $this->invitations()->count();

        return $number > 0 ? $number : 0;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes[self::PASSWORD] = Hash::make($value);
    }

    public function verificationCode()
    {
        return $this->hasOne(VerificationCode::class);
    }

    public function passwordResetToken()
    {
        return $this->morphOne(PasswordResetToken::class, 'model');
    }

    public function places()
    {
        return $this->hasMany(Place::class);
    }

    public function tripOrder()
    {
        return $this->hasOne(TripOrder::class);
    }

    public function devices() {
        return $this->morphMany(Device::class, 'model');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function avatar()
    {
        return $this->morphOne(Avatar::class, 'model');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}
