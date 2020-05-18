<?php

namespace Tests\Feature\Client;

use App\Constants\Disk;
use App\Http\Resources\ClientResource;
use App\Models\Avatar;
use App\Models\Client;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
            Avatar::FILE_INPUT_NAME => UploadedFile::fake()->image('fake.jpg')
        ];

        $response = $this->patchJson(
            route('clients.update', ['client' => $client->id]),
            $data
        );

        unset($data[Client::PASSWORD]);
        unset($data[Client::BIRTHDAY]);
        unset($data[Avatar::FILE_INPUT_NAME]);
        $data[Client::ID] = $client->id;
        $data[Client::PHONE] = $client->phone;

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'data' => $data,
             ]);

        $this->assertDatabaseHas('avatars', ['model_id' => $client->id]);

        $client->refresh();
        Storage::disk(Disk::CLIENT_AVATARS)->assertExists($client->avatar->filename);
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
            Client::PASSWORD,
        ];

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'done',
                'errors' => $errors
            ]);
    }
}
