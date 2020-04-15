<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Services\ResetPasswordService;
use SMartins\PassportMultiauth\PassportMultiauth;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ClientResetPasswordTest extends TestCase
{
    public function testResetPassword()
    {
        $client = factory(Client::class)->create();
        $passwordService = $this->app->make(ResetPasswordService::class);
        $passwordService->setModel($client);
        $token = $passwordService->create();

        PassportMultiauth::actingAs($client, ['access-client']);

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
