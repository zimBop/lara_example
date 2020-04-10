<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PasswordResetToken
 *
 * @property int $client_id
 * @property string $token
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereToken($value)
 * @mixin \Eloquent
 */
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
