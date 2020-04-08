<?php

namespace Tests\Feature\Client;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\ClientService;
use App\Services\NexmoService;
use App\Services\VerificationCodeService;
use Tests\TestCase;
use Mockery;

class ClientRegistrationTest extends TestCase
{
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

        $message = 'SMS was successfully sent';
        $nexmoMock->shouldReceive('sendSMS')->once()->andReturn(['sent' => true, 'message' => $message]);

        $response = $this->postJson(route('clients.store'), [
            'phone' => $phone
        ]);

        $this->assertDatabaseHas('clients', [
            'phone' => $phone,
        ]);

        $client = Client::wherePhone($phone)->first();

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => $message,
                'client' => (new ClientResource($client))->toArray(null),
                'is_registration_completed' => false,
             ]);

        $this->checkSmsSendingDelay($phone);
    }

    protected function checkResponseOnNexmoError($nexmoMock, $phone)
    {
        $error = 'error';
        $nexmoMock->shouldReceive('sendSMS')
            ->once()->andReturn(['sent' => false, 'message' => $error]);

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

    protected function createNexmoMock()
    {
        $nexmoMock = Mockery::mock(NexmoService::class);
        $this->app->instance(NexmoService::class, $nexmoMock);

        return $nexmoMock;
    }
}
