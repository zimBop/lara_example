<?php

namespace Tests\Feature\Trip;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Shift;
use App\Models\Trip;
use App\Models\TripOrder;
use App\Notifications\TripStatusChanged;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class TripTest extends TestCase
{
    public function testIsTripSuccessfullyCanceled(): void
    {
        $client = $this->makeAuthClient();
        $status = $this->faker->randomElement([
            TripStatuses::DRIVER_IS_ON_THE_WAY,
            TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT,
        ]);
        $models = $this->prepareModels($client, $status);

        $response = $this->postJson(
            route('trip.cancel', ['client' => $client->id])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => TripMessages::CANCELED,
            ]);

        $this->assertDatabaseMissing('trip_orders', ['id' => $models['tripOrder']->id]);
        $this->assertSoftDeleted('trips', ['id' => $models['trip']->id]);
    }

    public function testIsTripInProgressCannotBeCanceled(): void
    {
        $client = $this->makeAuthClient();
        $status = $this->faker->randomElement([
            TripStatuses::TRIP_IN_PROGRESS,
            TripStatuses::UNRATED,
        ]);

        $models = $this->prepareModels($client, $status);

        $response = $this->postJson(
            route('trip.cancel', ['client' => $client->id])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => TripMessages::CANNOT_BE_CANCELED,
            ]);

        $this->assertDatabaseHas('trip_orders', ['id' => $models['tripOrder']->id]);
        $this->assertDatabaseHas('trips', ['id' => $models['trip']->id]);
    }

    public function testDriverArrived()
    {
        $driver = $this->makeAuthDriver();
        $client = factory(Client::class)->create();

        $models = $this->prepareModels($client, TripStatuses::DRIVER_IS_ON_THE_WAY, $driver);

        Notification::fake();

        $response = $this->postJson(
            route('trip.arrived', ['driver' => $driver->id])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => TripMessages::DRIVER_ARRIVED,
            ]);

        Notification::assertSentTo($client, TripStatusChanged::class);

        $this->assertDatabaseHas('trip_orders', ['id' => $models['tripOrder']->id, 'status' => TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT]);
        $this->assertDatabaseHas('trips', ['id' => $models['trip']->id, 'status' => TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT]);
    }

    protected function prepareModels(Client $client, int $status, Driver $driver = null): array
    {
        $tripOrder = factory(TripOrder::class)->create(
            [
                TripOrder::CLIENT_ID => $client->id,
                TripOrder::STATUS => $status,
            ]
        );

        $driver = $driver ?: factory(Driver::class)->create();
        $shift = factory(Shift::class)->create([
            Shift::DRIVER_ID => $driver->id
        ]);

        $trip = factory(Trip::class)->create([
            Trip::SHIFT_ID => $shift->id,
            Trip::CLIENT_ID => $client->id,
            Trip::STATUS => $status
        ]);

        return ['trip' => $trip, 'tripOrder' => $tripOrder];
    }
}
