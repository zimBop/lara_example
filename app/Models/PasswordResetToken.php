<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PasswordResetToken
 *
 * @property int $id
 * @property string $token
 * @property int $model_id
 * @property string $model_type
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereToken($value)
 * @mixin \Eloquent
 * @property string|null $email
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordResetToken whereEmail($value)
 */
class PasswordResetToken extends Model
{
    public const CREATED_AT = 'created_at';
    public const TOKEN = 'token';
    public const EMAIL = 'email';

    protected $fillable = [
        self::CREATED_AT,
        self::TOKEN,
        self::EMAIL,
    ];

    public $timestamps = false;

    protected $dates = [
        self::CREATED_AT,
    ];

    /**
     * Get the owning model.
     */
    public function model()
    {
        return $this->morphTo();
    }
}
