<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VerificationCode
 *
 * @property int $id
 * @property string $code
 * @property int $client_id
 * @property \Illuminate\Support\Carbon $expires_at
 * @property string|null $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $is_expired
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereVerifiedAt($value)
 * @mixin \Eloquent
 */
class VerificationCode extends Model
{
    public const CODE = 'code';
    public const CLIENT_ID = 'client_id';
    public const EXPIRES_AT = 'expires_at';
    public const VERIFIED_AT = 'verified_at';

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
