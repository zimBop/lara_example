<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StripeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (config('services.stripe.skip_tests')) {
            $this->markTestSkipped('Stripe tests skipped. To change this behaviour set env variable STRIPE_SKIP_TESTS to false');
        }
    }

    public function testGetClientSecret()
    {
        $client = factory(Client::class)->create([
            Client::CUSTOMER_ID => config('services.stripe.test_customer_id')
        ]);

        Passport::actingAs($client, ['access-client']);

        $response = $this->getJson(
            route('clients.stripe-secret', ['client' => $client->id])
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'done',
                'data' => [
                    'secret'
                ],
            ]);
    }
}
