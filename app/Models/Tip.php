<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
