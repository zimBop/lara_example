<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tip
 *
 * @property int $id
 * @property int|null $trip_id
 * @property int|null $driver_id
 * @property string $payment_method_id Stripe payment method id
 * @property int $amount Tip amount in cents.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip whereTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tip whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tip extends Model
{
    public const TRIP_ID = 'trip_id';
    public const DRIVER_ID = 'driver_id';
    public const PAYMENT_METHOD_ID = 'payment_method_id';
    public const AMOUNT = 'amount';

    protected $fillable = [
        self::TRIP_ID,
        self::DRIVER_ID,
        self::PAYMENT_METHOD_ID,
        self::AMOUNT,
    ];
}
