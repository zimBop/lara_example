<?php

namespace Tests\Feature\Trip;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Resources\TripResource;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Shift;
use App\Models\Tip;
use App\Models\Trip;
use App\Models\TripOrder;
use App\Notifications\TripCanceled;
use App\Notifications\TripStatusChanged;
use App\Services\StripeService;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class TripTest extends TestCase
{
    public function testIsTripSuccessfullyCanceledByClient(): void
    {
        $client = $this->makeAuthClient();
        $status = $this->faker->randomElement([
            TripStatuses::DRIVER_IS_ON_THE_WAY,
            TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT,
        ]);
        $models = $this->prepareModels($client, $status);

        $response = $this->postJson(
            route('trip.client-cancel', ['client' => $client->id])
        );

        $this->checkResponseMessage($response, TripMessages::CANCELED);

        $this->assertDatabaseMissing('trip_orders', ['id' => $models['tripOrder']->id]);
        $this->assertSoftDeleted('trips', ['id' => $models['trip']->id]);
    }

    public function testIsTripSuccessfullyCanceledByDriver(): void
    {
        $driver = $this->makeAuthDriver();
        $client = factory(Client::class)->create();
        $status = $this->faker->randomElement([
            TripStatuses::DRIVER_IS_ON_THE_WAY,
            TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT,
        ]);
        $models = $this->prepareModels($client, $status, $driver);

        Notification::fake();

        $response = $this->postJson(
            route('trip.driver-cancel', ['driver' => $driver->id])
        );

        Notification::assertSentTo($client, TripCanceled::class);

        $this->checkResponseMessage($response, TripMessages::CANCELED);

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
            route('trip.client-cancel', ['client' => $client->id])
        );

        $this->checkResponseMessage($response, TripMessages::CANNOT_BE_CANCELED);

        $this->assertDatabaseHas('trip_orders', ['id' => $models['tripOrder']->id]);
        $this->assertDatabaseHas('trips', ['id' => $models['trip']->id]);
    }

    // TripController->arrived(), ->finish(), ->archive()
    public function testIsTripStatusChanged()
    {
        $actions = [
            'arrived' => [
                'status' => TripStatuses::DRIVER_IS_ON_THE_WAY,
                'new_status' => TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT,
                'model' => 'driver'
            ],
            'finish' => [
                'status' => TripStatuses::TRIP_IN_PROGRESS,
                'new_status' => TripStatuses::UNRATED,
                'model' => 'driver'
            ],
            'archive' => [
                'status' => TripStatuses::UNRATED,
                'new_status' => TripStatuses::TRIP_ARCHIVED,
                'model' => 'client'
            ],
        ];

        Notification::fake();

        foreach ($actions as $action => $data) {
            $driver = $data['model'] === 'driver' ? $this->makeAuthDriver() : factory(Driver::class)->create();
            $client = $data['model'] === 'client' ? $this->makeAuthClient() : factory(Client::class)->create();
            $models = $this->prepareModels($client, $data['status'], $driver);
            $response = $this->postJson(
                route('trip.' . $action, [$data['model'] => $data['model'] === 'driver' ? $driver->id : $client->id])
            );

            $this->checkResponse($response, $models['trip']->refresh());

            Notification::assertSentTo($client, TripStatusChanged::class);

            $this->checkStatus($models, $data['new_status']);
        }

    }

    // TripController->start()
    public function testIsClientPickedUp()
    {
        $driver = $this->makeAuthDriver();
        $client = factory(Client::class)->create();

        $models = $this->prepareModels($client, TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT, $driver);

        Notification::fake();
        $this->setupStripeMock();

        $response = $this->postJson(
            route('trip.start', ['driver' => $driver->id])
        );

        $this->checkResponse($response, $models['trip']->refresh());

        Notification::assertSentTo($client, TripStatusChanged::class);

        $this->checkStatus($models, TripStatuses::TRIP_IN_PROGRESS);

        $models['trip']->refresh();
        $this->assertNotEquals(null, $models['trip']->picked_up_at);
    }

    // TripController->start()
    public function testIsFreeTripApplied(): void
    {
        $driver = $this->makeAuthDriver();

        $freeTripsNumber = $this->faker->numberBetween(1, 3);
        $client = factory(Client::class)->create([
            Client::FREE_TRIPS => $freeTripsNumber
        ]);

        $this->prepareModels($client, TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT, $driver, true);

        $this->postJson(
            route('trip.start', ['driver' => $driver->id])
        );

        $client->refresh();
        $this->assertEquals($freeTripsNumber - 1, $client->free_trips);
    }

    public function testIsDriverRated()
    {
        $client = $this->makeAuthClient();
        $driver = factory(Driver::class)->create();

        $models = $this->prepareModels($client, TripStatuses::UNRATED, $driver);

        Notification::fake();
        $this->setupStripeMock();

        $params = [
            'trip_id' => $models['trip']->id,
            'rating' => $this->faker->numberBetween(1, 5),
            'payment_method_id' => 'payment_method_id',
            'amount' => $this->faker->numberBetween(),
            'comment' => $this->faker->sentence,
        ];

        $response = $this->postJson(
            route('trip.rate', ['client' => $client->id]),
            $params
        );

        $this->checkResponseMessage($response, TripMessages::DRIVER_RATED);

        Notification::assertSentTo($client, TripStatusChanged::class);

        $this->checkStatus($models, TripStatuses::TRIP_ARCHIVED);

        $this->assertDatabaseHas('tips', [
            Tip::TRIP_ID => $params['trip_id'],
            Tip::PAYMENT_METHOD_ID => $params['payment_method_id'],
            Tip::AMOUNT => $params['amount'],
        ]);

        $driver->refresh();

        $this->assertEquals($params['rating'], $driver->rating);

        $client->refresh();
        $this->assertEquals($client->trips()->archived()->get()->sum('co2'), $client->co2_sum);
    }

    protected function prepareModels(Client $client, int $status, Driver $driver = null, $freeTrip = false): array
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

        $tripData = [
            Trip::SHIFT_ID => $shift->id,
            Trip::CLIENT_ID => $client->id,
            Trip::STATUS => $status
        ];

        if ($freeTrip) {
            $tripData[Trip::PAYMENT_METHOD_ID] = null;
        }

        $trip = factory(Trip::class)->create($tripData);

        return ['trip' => $trip, 'tripOrder' => $tripOrder];
    }

    protected function checkStatus($models, $status): void
    {
        if ($status !== 7) {
            $this->assertDatabaseHas('trip_orders', ['id' => $models['tripOrder']->id, 'status' => $status]);
        }
        $this->assertDatabaseHas('trips', ['id' => $models['trip']->id, 'status' => $status]);
    }

    protected function checkResponse($response, $trip)
    {
        $encodedResource = (new TripResource($trip))->response()->getContent();
        $data = json_decode($encodedResource, true);

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => $data
            ]);
    }

    protected function checkResponseMessage($response, $message)
    {
        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => $message,
            ]);
    }

    protected function setupStripeMock(): void
    {
        $stripeMock = \Mockery::mock(StripeService::class)->makePartial();
        $stripeMock->shouldReceive('makePayment')->andReturn('');
        $this->app->instance(StripeService::class, $stripeMock);
    }
}
