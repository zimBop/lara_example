<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Services\ResetPasswordService;
use Illuminate\Support\Facades\Hash;

class ClientResetPasswordTest extends ClientTestCase
{
    public function testResetPassword()
    {
        $client = $this->makeAuthClient();
        $passwordService = $this->app->make(ResetPasswordService::class);
        $passwordService->setModel($client);
        $token = $passwordService->create();

        $newPassword = $this->faker->password;

        $data = [
            Client::PASSWORD => $newPassword,
            'token' => $token,
        ];

        $response = $this->patchJson(
            route('clients.reset-password', ['client' => $client->id]),
            $data
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'message' => 'Password reset successfully.',
             ]);

        $client->refresh();
        $this->assertTrue(Hash::check($newPassword, $client->password));

        $this->checkExpiredToken($client, $data);
    }

    protected function checkExpiredToken(Client $client, array $data)
    {
        $tokenModel = $client->passwordResetToken;
        $tokenLifetime = config('app.password_reset.token_lifetime');
        $tokenModel->created_at = $tokenModel->created_at->subMinutes($tokenLifetime);
        $tokenModel->save();

        $response = $this->patchJson(
            route('clients.reset-password', ['client' => $client->id]),
            $data
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => false,
                 'message' => 'Password reset token expired or not exists.',
             ]);
    }
}
