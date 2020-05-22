<?php

namespace Tests\Feature\Client;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\VerificationCode;
use App\Services\ClientService;
use App\Services\VerificationCodeService;
use Tests\Feature\Traits\Nexmo;
use Tests\TestCase;

class ClientRegistrationTest extends TestCase
{
    use Nexmo;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSuccessfulRegistration()
    {
        $nexmoMock = $this->createNexmoMock();
        $phone = ClientService::generatePhoneNumber();

        $this->checkResponseOnNexmoError($nexmoMock, $phone);

        $this->setupSuccessfulSmsSending($nexmoMock);

        $response = $this->postJson(route('clients.store'), [
            'phone' => $phone
        ]);

        $this->assertDatabaseHas('clients', [
            'phone' => $phone,
        ]);

        $client = Client::wherePhone($phone)->first();
        $this->assertDatabaseHas('verification_codes', [
            VerificationCode::CLIENT_ID => $client->id,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => [
                    'client' => (new ClientResource($client))->toArray(null),
                    'is_registration_completed' => false,
                ],
             ]);

        $this->checkSmsSendingDelay($phone);
    }

    protected function checkResponseOnNexmoError($nexmoMock, $phone)
    {
        $error = 'error';
        $this->setupSmsSendingWithError($nexmoMock, $error);

        $response = $this->postJson(route('clients.store'), [
            'phone' => $phone
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => false,
                 'message' => $error,
             ]);

        Client::wherePhone($phone)->first()->delete();
    }

    protected function checkSmsSendingDelay($phone) {
        $response = $this->postJson(route('clients.store'), [
            'phone' => $phone
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => false,
                'message' => VerificationCodeService::getCannotSendMessage(),
            ]);
    }

    public function testRegistrationWithEmptyPhone()
    {
        $response = $this->postJson(route('clients.store'), []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'done',
                'message',
                'errors' => [
                    'phone'
                ],
            ]);
    }
}
