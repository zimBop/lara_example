<?php

namespace App\Services;

use App\Models\Client;

class ClientService
{
    protected $client;

    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Change Client's status from current to opposite one 1 > 0, 0 > 1
     * @return true | false
     */
    public function changeActivity(): bool
    {
        return (bool) $this->client->update([Client::IS_ACTIVE => !$this->client->is_active]);
    }

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