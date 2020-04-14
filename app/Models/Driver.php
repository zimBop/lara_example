<?php

namespace App\Models;

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
 */
class Driver extends Authenticatable
{
    use HasMultiAuthApiTokens, Notifiable;

    public const ID = 'id';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const IS_ACTIVE = 'is_active';

    protected $fillable = [
        self::EMAIL,
        self::PASSWORD,
        self::FIRST_NAME,
        self::LAST_NAME,
        self::IS_ACTIVE,
    ];

    /**
     * Full Name attribute getter
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes[self::PASSWORD] = Hash::make($value);
    }
}
