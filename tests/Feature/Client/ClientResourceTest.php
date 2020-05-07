<?php

namespace Tests\Feature\Client;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Tests\TestCase;

class ClientResourceTest extends TestCase
{
    public function testGetClient()
    {
        $clientOne = $this->makeAuthClient();

        $response = $this->getJson(route('clients.show', ['client' => $clientOne->id]));

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'data' => [
                     'client' => (new ClientResource($clientOne))->toArray(null),
                     'is_registration_completed' => $clientOne->isRegistrationCompleted(),
                 ],
             ]);

        $clientTwo = factory(Client::class)->create();
        $response = $this->getJson(route('clients.show', ['client' => $clientTwo->id]));

        $response
            ->assertStatus(403)
            ->assertJsonFragment([
                 'done' => false,
             ]);
    }

    public function testDeleteClient()
    {
        $client = $this->makeAuthClient();

        $response = $this->deleteJson(route('clients.destroy', ['client' => $client->id]));

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'message' => "Client with ID = {$client->id} deleted.",
             ]);

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    public function testPatchClient()
    {
        $client = $this->makeAuthClient([
            Client::FIRST_NAME => null,
            Client::LAST_NAME => null,
            Client::PASSWORD => null,
        ]);

        $this->checkPatchClientValidationErrors($client);

        $data = [
            Client::FIRST_NAME => $this->faker->firstName,
            Client::LAST_NAME => $this->faker->lastName,
            Client::EMAIL => $this->faker->email,
            Client::BIRTHDAY => $this->faker->date('m/d/Y'),
            Client::PASSWORD => $this->faker->password(6),
        ];

        $response = $this->patchJson(
            route('clients.update', ['client' => $client->id]),
            $data
        );

        unset($data[Client::PASSWORD]);
        unset($data[Client::BIRTHDAY]);
        $data[Client::ID] = $client->id;
        $data[Client::PHONE] = $client->phone;

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'data' => $data,
             ]);

        // Password not required in case $client->isRegistrationCompleted()
        // So errors will differ and we must check them again
        $this->checkPatchClientValidationErrors($client);
    }

    protected function checkPatchClientValidationErrors(Client $client)
    {
        $client->refresh();

        $response = $this->patchJson(
            route('clients.update', ['client' => $client->id]),
            []
        );

        $errors = [
            Client::FIRST_NAME,
            Client::LAST_NAME,
        ];

        if (!$client->isRegistrationCompleted()) {
            $errors[] = Client::PASSWORD;
        }

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'done',
                'errors' => $errors
            ]);
    }
}
