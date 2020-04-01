<?php

namespace App\Services;

use App\Models\VerificationCode;
use App\Models\Client;

class VerificationCodeService
{
    protected $client;
    protected $canSend;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function canSend(): bool
    {
        if (!$this->client->verificationCode) {
            $this->canSend = true;

            return true;
        }

        $this->canSend = $this->checkDelayPassed();

        return $this->canSend;
    }

    public function get(): VerificationCode
    {
        if (!$this->client->verificationCode) {
            return $this->create();
        }

        if ($this->client->verificationCode->is_expired) {
            $this->client->verificationCode->delete();

            return $this->create();
        }

        if ($this->canSend) {
            $this->update();
        }

        return $this->client->verificationCode;
    }

    public function update()
    {
        $newCode = self::generate();
        $this->client->verificationCode->update([VerificationCode::CODE => $newCode]);
    }

    protected function create(): VerificationCode
    {
        return factory(VerificationCode::class)->create(
            [VerificationCode::CLIENT_ID => $this->client->id]
        );
    }

    public function checkDelayPassed()
    {
        $delayEnd = $this->client->verificationCode
            ->updated_at
            ->addMinutes(VerificationCode::DELAY_MINUTES);

        return now()->gt($delayEnd);
    }

    public static function generate(): string
    {
        return str_pad(
            rand(0, pow(10, VerificationCode::CODE_LENGTH) - 1),
            VerificationCode::CODE_LENGTH,
            '0',
            STR_PAD_LEFT
        );
    }
}
