<?php

namespace App\Constants;

class VehicleConstants
{
    public const COLOR_WHITE = 1;
    public const COLOR_BLACK = 2;
    public const COLOR_SILVER = 3;
    public const COLOR_BLUE = 4;
    public const COLOR_RED = 5;

    public const COLORS = [
        self::COLOR_WHITE => ['name' => 'white', 'hex' => '#FFFFFF'],
        self::COLOR_BLACK => ['name' => 'black', 'hex' => '#000000'],
        self::COLOR_SILVER => ['name' => 'silver', 'hex' => '#494A52'],
        self::COLOR_BLUE => ['name' => 'blue', 'hex' => '#00328D'],
        self::COLOR_RED => ['name' => 'red', 'hex' => '#A2001A'],
    ];

    public const STATUS_AVAILABLE = 1;
    public const STATUS_OUT = 2;
    public const STATUS_MAINTENANCE = 3;

    public const STATUSES = [
        self::STATUS_AVAILABLE => ['name' => 'available', 'badge' => 'success'],
        self::STATUS_OUT => ['name' => 'out', 'badge' => 'primary'],
        self::STATUS_MAINTENANCE => ['name' => 'maintenance', 'badge' => 'warning'],
    ];

    public const BRANDS = [
        1 => ['name' => 'Tesla', 'models' => [1 => 'Model S', 2 => 'Model 3', 3 => 'Model X', 4 => 'Model Y']],
    ];
}
