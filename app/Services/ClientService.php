<?php

namespace App\Services;

class ClientService
{
    /**
     * Generate random phone number in format: +1 (XXX) XXX-XXXX
     *
     * @return string
     */
    public static function generatePhoneNumber(): string
    {
        return '+1 (' . self::generateRandomInt(3) . ') '
            . self::generateRandomInt(3) . '-' . self::generateRandomInt(4);
    }

    public static function generateRandomInt(int $digitsNumber): int
    {
        return rand(pow(10, $digitsNumber - 1) - 1, pow(10, $digitsNumber) - 1);
    }

}
