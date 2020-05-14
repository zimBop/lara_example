<?php

namespace App\Models;

use App\Constants\TripStatuses;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use SMartins\PassportMultiauth\HasMultiAuthApiTokens;

/**
 * App\Models\Driver
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $full_name
 * @property-read \App\Models\PasswordResetToken $passwordResetToken
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Driver onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Driver whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Driver withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Driver withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Device[] $devices
 * @property-read int|null $devices_count
 * @property-read mixed $active_shift
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Shift[] $shifts
 * @property-read int|null $shifts_count
 * @property-read mixed $active_trip
 */
class Driver extends Authenticatable
{
    use HasMultiAuthApiTokens, Notifiable, SoftDeletes, CanReceiveIosPush, CanBeRated;

    public const ID = 'id';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const IS_ACTIVE = 'is_active';
    public const RATING = 'rating';
    public const PHONE = 'phone';

    protected $fillable = [
        self::EMAIL,
        self::PASSWORD,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::IS_ACTIVE,
        self::PHONE,
        self::RATING,
    ];

    protected $casts = [
        self::RATING => 'float',
    ];

    /**
     * Full Name attribute getter
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }

    public function getActiveShiftAttribute()
    {
        return $this->shifts()->active()->first();
    }

    public function getActiveTripAttribute()
    {
        if (!$this->active_shift) {
            return null;
        }

        return $this->active_shift
            ->trips()
            ->active()
            ->where(Trip::STATUS, '<', TripStatuses::UNRATED)
            ->first();
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes[self::PASSWORD] = Hash::make($value);
    }

    public function passwordResetToken()
    {
        return $this->morphOne(PasswordResetToken::class, 'model');
    }

    public function devices()
    {
        return $this->morphMany(Device::class, 'model');
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function tips()
    {
        return $this->hasMany(Tip::class);
    }
}
