<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Invitation
 *
 * @property int $id
 * @property int $client_id
 * @property string $phone
 * @property bool $accepted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation whereAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property bool $sms_sent SMS with info about invite sent to the friend's phone
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invitation whereSmsSent($value)
 * @property-read \App\Models\Client $sender
 */
class Invitation extends Model
{
    public const CLIENT_ID = 'client_id';
    public const PHONE = 'phone';
    public const ACCEPTED = 'accepted';
    public const SMS_SENT = 'sms_sent';

    protected $fillable = [
        self::PHONE,
        self::ACCEPTED,
        self::SMS_SENT,
    ];

    protected $casts = [
        self::ACCEPTED => 'boolean',
        self::SMS_SENT => 'boolean',
    ];

    public function sender()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
