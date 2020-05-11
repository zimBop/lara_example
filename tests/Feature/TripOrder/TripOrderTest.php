<?php

namespace Tests\Feature\TripOrder;

use App\Constants\TripStatuses;
use App\Http\Resources\DriverResource;
use App\Http\Resources\TripOrderResource;
use App\Http\Resources\TripResource;
use App\Http\Resources\VehicleResource;
use App\Models\Shift;
use App\Models\Trip;
use App\Models\TripOrder;
use App\Notifications\TripOrderAccepted;
use GoogleMaps\Directions;
use SMartins\PassportMultiauth\PassportMultiauth;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class TripOrderTest extends TestCase
{
    protected const REQUEST_DATA = [
        TripOrder::ORIGIN => [
            'id' => 'place_id:ChIJTYPjdjRV5okRIn-weMnRXXw',
            'label' => 'test origin label',
        ],
        TripOrder::DESTINATION => [
            'id' => 'place_id:ChIJzeZ1iHyr54kR4tA85sx6RGM',
            'label' => 'test destination label',
        ],
        TripOrder::WAYPOINTS => [
            [
                'id' => 'place_id:ChIJk1_Sr3Gr54kRcACgP1AcAgM',
                'label' => 'test waypoint1 label',
            ],
            [
                'id' => '41.790789,-72.746783',
                'label' => 'test waypoint1 label',
            ],
        ],
    ];

    public function testIsTripOrderSuccessfullyAdded(): void
    {
        $client = $this->makeAuthClient();

        $directionsMock = $this->setupDirectionsMock();
        $this->setupGoogleMapsMock($directionsMock);

        $response = $this->postJson(
            route('trip-order.store', ['client' => $client->id]),
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
            route('trip-order.store', ['client' => $client->id]),
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
            route('trip-order.store', ['client' => $client->id]),
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
            route('trip-order.show', ['client' => $client->id])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => (new TripOrderResource($tripOrder))->toArray(null)
            ]);
    }

    public function testIsTripOrderSuccessfullyConfirmed(): void
    {
        $client = $this->makeAuthClient();

        $tripOrder = factory(TripOrder::class)->create(
            [
                TripOrder::CLIENT_ID => $client->id,
                TripOrder::STATUS => TripStatuses::WAITING_FOR_CONFIRMATION,
            ]
        );

        $response = $this->postJson(
            route('trip-order.confirm', ['client' => $client->id]),
            [
                TripOrder::PAYMENT_METHOD_ID => 'test payment method',
                TripOrder::MESSAGE_FOR_DRIVER => 'test message for driver',
            ]
        );

        $tripOrder->refresh();

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => (new TripOrderResource($tripOrder))->toArray(null)
            ]);

        $this->assertDatabaseHas('trip_orders', ['id' => $tripOrder->id, 'status' => TripStatuses::LOOKING_FOR_DRIVER]);
    }

    public function testIsTripOrderNotFoundErrorShown(): void
    {
        $client = $this->makeAuthClient();

        $response = $this->postJson(
            route('trip-order.confirm', ['client' => $client->id]),
            [TripOrder::PAYMENT_METHOD_ID => 'test payment method']
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => 'Trip Request not found.'
            ]);
    }

    public function testIsTripOrderSuccessfullyAccepted(): void
    {
        $directionsMock = $this->setupDirectionsMock();
        $this->setupGoogleMapsMock($directionsMock);
        Notification::fake();

        $driver = $this->makeAuthDriver();

        $tripOrder = factory(TripOrder::class)
            ->create([TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER]);

        factory(Shift::class)->create([
            Shift::DRIVER_ID => $driver->id
        ]);

        $response = $this->postJson(
            route('trip-order.accept', ['driver' => $driver->id, 'tripOrder' => $tripOrder->id])
        );

        $client = $tripOrder->client;
        Notification::assertSentTo($client, TripOrderAccepted::class);

        $tripOrder->refresh();
        $this->assertEquals(TripStatuses::DRIVER_IS_ON_THE_WAY, $tripOrder->status);

        $data = (new TripResource($client->activeTrip))->toArray(null);
        $data['driver'] = (new DriverResource($driver))->toArray(null);
        $data['vehicle'] = (new VehicleResource($driver->active_shift->vehicle))->toArray(null);

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => $data
            ]);

        // check Trip available after TripOrder accepted
        // TODO move this check to separate test
        PassportMultiauth::actingAs($client, ['access-client']);

        $response = $this->getJson(
            route('trip-order.show', ['client' => $client->id])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => $data
            ]);
    }

    public function testIsDriverDoesntHaveActiveShiftErrorShown(): void
    {
        $driver = $this->makeAuthDriver();

        $tripOrder = factory(TripOrder::class)
            ->create([TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER]);

        $response = $this->postJson(
            route('trip-order.accept', [
                'driver' => $driver->id,
                'tripOrder' => $tripOrder->id,
            ])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => false,
                'message' => 'Driver doesnt have active shift.'
            ]);
    }

    public function testIsDriverAlreadyHasActiveTripErrorShown(): void
    {
        $driver = $this->makeAuthDriver();

        $tripOrder = factory(TripOrder::class)->create([
            TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER,
        ]);

        $shift = factory(Shift::class)->create([
            Shift::DRIVER_ID => $driver->id,
        ]);

        factory(Trip::class)->create([
            Trip::SHIFT_ID => $shift->id,
            Trip::STATUS => TripStatuses::DRIVER_IS_ON_THE_WAY
        ]);

        $response = $this->postJson(
            route('trip-order.accept', [
                'driver' => $driver->id,
                'tripOrder' => $tripOrder->id,
            ])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => false,
                'message' => 'Driver already has active trips.'
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