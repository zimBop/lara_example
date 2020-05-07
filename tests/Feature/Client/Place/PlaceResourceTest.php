<?php

namespace Tests\Feature\Client\Place;

use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Tests\TestCase;

class PlaceResourceTest extends TestCase
{
    public function testIsPlacesListAvailable(): void
    {
        $client = $this->makeAuthClient();

        $placesAmount = 3;

        factory(Place::class, $placesAmount)->create([ 'client_id' => $client->id]);

        $placesCollection = PlaceResource::collection($client->places);

        $response = $this->getJson(route('clients.places.index', ['client' => $client->id]));

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => $placesCollection->toArray(null)
            ]);

        $responsePlacesAmount = count($response->json()['data']);

        $this->assertEquals($responsePlacesAmount, $placesAmount);
    }

    public function testIsClientPlaceAvailable(): void
    {
        $client = $this->makeAuthClient();

        $place = factory(Place::class)->create([ 'client_id' => $client->id]);

        $response = $this->getJson(route('clients.places.show', [
            'client' => $client->id,
            'place' => $place->id,
        ]));

        $response
            ->assertStatus(200)
            ->assertJson([
                 'done' => true,
                 'data' => (new PlaceResource($place))->toArray(null)
             ]);
    }

    public function testIsOtherClientPlaceNotAvailable(): void
    {
        $clientOne = $this->makeAuthClient();

        $clientTwo = $this->makeAuthClient();

        $clientTwoPlace = factory(Place::class)->create([ 'client_id' => $clientTwo->id]);

        $response = $this->getJson(route('clients.places.show', [
            'client' => $clientOne->id,
            'place' => $clientTwoPlace->id,
        ]));

        $response
            ->assertStatus(403)
            ->assertJsonFragment([
                'done' => false,
            ]);
    }

    public function testIsPlaceSuccessfullyAdded(): void
    {
        $client = $this->makeAuthClient();

        $data = [
            Place::NAME => $this->faker->word,
            Place::DESCRIPTION => $this->faker->address,
            Place::PLACE_ID => $this->faker->word,
        ];

        $response = $this->postJson(
            route('clients.places.store', ['client' => $client->id]),
            $data
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => 'New favorite place is successfully added.'
            ]);
    }

    public function testIsPlaceSuccessfullyDeleted(): void
    {
        $client = $this->makeAuthClient();

        $place = factory(Place::class)->create([ 'client_id' => $client->id]);

        $response = $this->deleteJson(
            route(
                'clients.places.destroy',
                [
                    'client' => $client->id,
                    'place' => $place->id,
                ]
            )
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => "Place '{$place->name}' is deleted.",
            ]);

        $this->assertDatabaseMissing('places', [
            'id' => $place->id,
        ]);
    }
}
