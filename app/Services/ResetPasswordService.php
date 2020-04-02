<?php

namespace App\Services;

use App\Models\Client;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResetPasswordService
{
    protected $client;

    /**
     * @param mixed $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function create(): string
    {
        $token = $this->generateToken();

        if ($this->client->passwordResetToken) {
            $this->client->passwordResetToken->delete();
        }

        PasswordResetToken::create([
            PasswordResetToken::CLIENT_ID => $this->client->id,
            PasswordResetToken::TOKEN => Hash::make($token),
            PasswordResetToken::CREATED_AT => now(),
        ]);

        return $token;
    }

    /**
     * Create a new token for the client.
     *
     * @return string
     */
    public function generateToken()
    {
        return hash_hmac('sha256', Str::random(40), config('app.key'));
    }

    /**
     * @param PasswordResetToken $token
     * @return mixed
     */
    protected function tokenExpired(PasswordResetToken $token)
    {
        $tokenLifetime = config('app.password_reset.token_lifetime');

        return $token->created_at->addMinutes($tokenLifetime)->isPast();
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  string  $token
     * @return bool
     */
    public function exists(string $token)
    {
        $tokenModel = $this->client->passwordResetToken;

        return $tokenModel && !$this->tokenExpired($tokenModel) &&
            Hash::check($token, $tokenModel->token);
    }
}
