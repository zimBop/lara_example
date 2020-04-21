<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use SMartins\PassportMultiauth\PassportMultiauth;
use Tests\TestCase;

class ClientTestCase extends TestCase
{
    protected function makeAuthClient(array $data = []): Client
    {
        $client = factory(Client::class)->create($data);

        PassportMultiauth::actingAs($client, ['access-client']);

        return $client;
    }
}
