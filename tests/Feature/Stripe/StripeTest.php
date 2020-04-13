<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use SMartins\PassportMultiauth\PassportMultiauth;
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

    public function testGetEphemeralKey()
    {
        $client = factory(Client::class)->create([
            Client::CUSTOMER_ID => config('services.stripe.test_customer_id')
        ]);

        PassportMultiauth::actingAs($client, ['access-client']);

        $response = $this->getJson(
            route('stripe.ephemeral-key', ['client' => $client->id])
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'done',
                'data' => [],
            ]);
    }

    public function testGetPaymentIntent()
    {
        $client = factory(Client::class)->create([
            Client::CUSTOMER_ID => config('services.stripe.test_customer_id')
         ]);

        PassportMultiauth::actingAs($client, ['access-client']);

        $response = $this->getJson(
            route('stripe.payment-intent', ['client' => $client->id])
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'done',
                'data' => [],
            ]);
    }
}
