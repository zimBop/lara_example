<?php

namespace App\Services;

use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResetPasswordService
{
    protected $model;

    /**
     * @param mixed $model
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function create(): string
    {
        $token = $this->generateToken();

        if ($this->model->passwordResetToken) {
            $this->model->passwordResetToken->delete();
        }

        $this->model->passwordResetToken()->create([
            PasswordResetToken::TOKEN => Hash::make($token),
            PasswordResetToken::CREATED_AT => now(),
            PasswordResetToken::EMAIL => $this->model->email
         ]);

        return $token;
    }

    /**
     * Create a new token for the model.
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
        $tokenModel = $this->model->passwordResetToken;

        return $tokenModel && !$this->tokenExpired($tokenModel) &&
            Hash::check($token, $tokenModel->token);
    }

    public function setNewPassword($input)
    {
        $token = $input['token'];

        if (!$this->exists($token)) {
            throw new HttpException(200, 'Password reset token expired or not exists.');
        }

        $this->model->update([
            'password' => $input['password']
        ]);
    }
}
