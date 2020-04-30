<?php

namespace App\Constants;

use ReflectionClass;

class Constants
{
    public static function getList(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}
