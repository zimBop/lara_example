<?php

namespace Tests\Feature\Client\TripOrder;

use App\Http\Resources\TripOrderResource;
use App\Models\TripOrder;
use GoogleMaps\Directions;
use Tests\Feature\Client\ClientTestCase;

class TripOrderTest extends ClientTestCase
{
    protected const REQUEST_DATA = [
        TripOrder::ORIGIN => 'place_id:ChIJTYPjdjRV5okRIn-weMnRXXw',
        TripOrder::DESTINATION => 'place_id:ChIJzeZ1iHyr54kR4tA85sx6RGM',
        TripOrder::WAYPOINTS => [
            'place_id:ChIJk1_Sr3Gr54kRcACgP1AcAgM',
            '41.790789,-72.746783'
        ],
    ];

    public function testIsTripOrderSuccessfullyAdded(): void
    {
        $client = $this->makeAuthClient();

        $directionsMock = $this->setupDirectionsMock();
        $this->setupGoogleMapsMock($directionsMock);

        $response = $this->postJson(
            route('trip.order.store', ['client' => $client->id]),
            self::REQUEST_DATA
        );

        $tripOrder = $client->tripOrder;

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => (new TripOrderResource($tripOrder))->toArray(null)
            ]);

        $this->assertDatabaseHas('trip_orders', ['id' => $tripOrder->id]);
    }

    public function testIsValidationErrorsReturned(): void
    {
        $client = $this->makeAuthClient();

        $response = $this->postJson(
            route('trip.order.store', ['client' => $client->id]),
            []
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                 'done' => false,
                 'errors' => [
                    'destination' => ['The destination field is required.'],
                    'origin' => ['The origin field is required.']
                 ],
                 'message' => 'The given data was invalid.'
             ]);
    }

    public function testIsDirectionsApiErrorReturned(): void
    {
        $client = $this->makeAuthClient();

        $directionsMock = $this->setupDirectionsMock('Error');
        $this->setupGoogleMapsMock($directionsMock);

        $response = $this->postJson(
            route('trip.order.store', ['client' => $client->id]),
            self::REQUEST_DATA
        );

        $response
            ->assertStatus(422)
            ->assertJsonFragment([
                'done' => false,
                'message' => 'Test error message'
            ]);
    }

    public function testIsTripOrderAvailable(): void
    {
        $client = $this->makeAuthClient();

        $tripOrder = factory(TripOrder::class)->create(
            [
                TripOrder::CLIENT_ID => $client->id
            ]
        );

        $response = $this->getJson(
            route('trip.order.show', ['client' => $client->id])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => (new TripOrderResource($tripOrder))->toArray(null)
            ]);
    }

    protected function setupGoogleMapsMock($directionsMock): void
    {
        \GoogleMaps::partialMock()
            ->shouldReceive('load')
            ->andReturn($directionsMock);
    }

    protected function setupDirectionsMock($type = 'Success'): object
    {
        $directionsMock = \Mockery::mock(Directions::class)
            ->makePartial();

        $fileName = '/clientDirections' . $type . '.json';

        $clientDirectionsApiResponse = file_get_contents(__DIR__ . $fileName);

        $driverDirectionsApiResponse = file_get_contents(__DIR__ . '/driverDirectionsSuccess.json');

        $directionsMock->shouldReceive('get')
            ->andReturn($clientDirectionsApiResponse, $driverDirectionsApiResponse);

        $directionsMock->shouldReceive('setParam')
            ->andReturn($directionsMock);

        return $directionsMock;
    }
}
