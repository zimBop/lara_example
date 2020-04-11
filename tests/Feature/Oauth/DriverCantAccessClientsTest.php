<?php

namespace Tests\Feature\Oauth;

use App\Models\Client;
use App\Models\Driver;
use SMartins\PassportMultiauth\PassportMultiauth;
use Tests\TestCase;

class DriverCantAccessClientsTest extends TestCase
{
    public function testDriverCannotAccessClient()
    {
        $client = factory(Client::class)->create();
        $driver = factory(Driver::class)->create();

        PassportMultiauth::actingAs($driver, ['access-driver']);

        $response = $this->getJson(route('clients.show', ['client' => $client->id]));

        $response
            ->assertStatus(401)
            ->assertJsonFragment(['done' => false]);
    }
}
