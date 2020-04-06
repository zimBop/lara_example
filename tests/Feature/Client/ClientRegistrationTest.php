<?php

namespace Tests\Feature\Client;

use App\Http\Resources\ClientResource;
use App\Models\Client;
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
        $mock = Mockery::mock(NexmoService::class);
        $message = 'SMS was successfully sent';
        $mock->shouldReceive('sendSMS')->once()->andReturn($message);
        $this->app->instance(NexmoService::class, $mock);

        $phone = $this->faker->phoneNumber;

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
