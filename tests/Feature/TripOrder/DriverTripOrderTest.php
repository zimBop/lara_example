<?php

namespace Tests\Feature\TripOrder;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Models\TripOrder;
use App\Notifications\TripOrderUpdated;
use App\Notifications\TripStatusChanged;
use Illuminate\Support\Facades\Notification;
use SMartins\PassportMultiauth\PassportMultiauth;
use Tests\TestCase;
use Tests\Feature\Traits\TripTrait;

class DriverTripOrderTest extends TestCase
{
    use TripTrait;

    public function testIsTripOrderSuccessfullyAccepted(): void
    {
        $directionsMock = $this->setupDirectionsMock();
        $this->setupGoogleMapsMock($directionsMock);
        Notification::fake();

        $driver = $this->createDriverAtWork(true);

        $tripOrder = factory(TripOrder::class)
            ->create([TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER]);

        $response = $this->postJson(
            route('trip-order.accept', ['driver' => $driver->id, 'tripOrder' => $tripOrder->id])
        );

        $client = $tripOrder->client;
        Notification::assertSentTo($client, TripStatusChanged::class);

        $tripOrder->refresh();
        $this->assertEquals(TripStatuses::DRIVER_IS_ON_THE_WAY, $tripOrder->status);
        $this->assertEquals($tripOrder->shifts()->count(), 0);

        $encodedResource = (new TripResource($client->activeTrip))->response()->getContent();
        $data = json_decode($encodedResource, true);

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => $data
            ]);

        // check Trip available after TripOrder accepted
        // TODO move this check to separate test
        PassportMultiauth::actingAs($client, ['access-client']);

        $this->getJson(
                route('trip-order.show', ['client' => $client->id])
            )->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => $data
            ]);
    }

    public function testIsTripOrdersUpdated(): void
    {
        $directionsMock = $this->setupDirectionsMock();
        $this->setupGoogleMapsMock($directionsMock);
        Notification::fake();

        $driver = $this->createDriverAtWork(true);
        $driverTwo = $this->createDriverAtWork();

        $tripOrderData = [
            TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER,
            TripOrder::EXPECTED_DRIVER_ID => $driver->id
        ];

        $firstTripOrder = factory(TripOrder::class)
            ->create($tripOrderData);

        $secondTripOrder = factory(TripOrder::class)
            ->create($tripOrderData);

        $this->postJson(
            route('trip-order.accept', ['driver' => $driver->id, 'tripOrder' => $firstTripOrder->id])
        );

        $client = $secondTripOrder->client;
        Notification::assertSentTo($client, TripOrderUpdated::class);

        $this->assertDatabaseHas('trip_orders', [
            TripOrder::ID => $secondTripOrder->id,
            TripOrder::EXPECTED_DRIVER_ID => $driverTwo->id
        ]);
    }

    public function testIsDriverDoesntHaveActiveShiftErrorShown(): void
    {
        $driver = $this->makeAuthDriver();

        $tripOrder = factory(TripOrder::class)
            ->create([TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER]);

        $this->postJson(
                route('trip-order.accept', [
                    'driver' => $driver->id,
                    'tripOrder' => $tripOrder->id,
                ])
            )->assertStatus(200)
            ->assertJson([
                'done' => false,
                'message' => TripMessages::DRIVER_HAS_NOT_SHIFT
            ]);
    }

    public function testIsDriverAlreadyHasActiveTripErrorShown(): void
    {
        $driver = $this->createDriverAtWork(true);

        $tripOrder = factory(TripOrder::class)->create([
            TripOrder::STATUS => TripStatuses::LOOKING_FOR_DRIVER,
        ]);

        factory(Trip::class)->create([
            Trip::SHIFT_ID => $driver->active_shift->id,
            Trip::STATUS => TripStatuses::DRIVER_IS_ON_THE_WAY
        ]);

        $this->postJson(
                route('trip-order.accept', [
                    'driver' => $driver->id,
                    'tripOrder' => $tripOrder->id,
                ])
            )->assertStatus(200)
            ->assertJson([
                'done' => false,
                'message' => TripMessages::DRIVER_ALREADY_HAS_ACTIVE_TRIP
            ]);
    }
}
