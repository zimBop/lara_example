<?php

namespace Tests\Feature\TripOrder;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Http\Resources\TripOrderResource;
use App\Models\Driver;
use App\Models\Shift;
use App\Models\TripOrder;
use App\Notifications\TripOrderCanceled;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Feature\Traits\TripTrait;

class ClientTripOrderTest extends TestCase
{
    use TripTrait;

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

        $this->createDriverAtWork();

        $directionsMock = $this->setupDirectionsMock();
        $this->setupGoogleMapsMock($directionsMock);

        $response = $this->postJson(
            route('trip-order.store', ['client' => $client->id]),
            self::REQUEST_DATA
        );

        $tripOrder = $client->tripOrder;

        $this->checkResponse($response, $tripOrder);

        $this->assertDatabaseHas('trip_orders', ['id' => $tripOrder->id]);
    }

    public function testIsRouteBoundsValidationFallen(): void
    {
        $client = $this->makeAuthClient();
        config(['app.skip_route_bounds_validation' => false]);

        $directionsMock = $this->setupDirectionsMock();
        $this->setupGoogleMapsMock($directionsMock);

        $this->postJson(
                route('trip-order.store', ['client' => $client->id]),
                self::REQUEST_DATA
            )->assertStatus(422)
            ->assertJson([
                'done' => false,
                'errors' => [
                    'route' => [TripMessages::ROUTE_BOUNDS_VALIDATION_ERROR],
                ],
                'message' => 'The given data was invalid.'
            ]);
    }

    public function testIsValidationErrorsReturned(): void
    {
        $client = $this->makeAuthClient();

        $this->postJson(
                route('trip-order.store', ['client' => $client->id]),
                []
            )->assertStatus(422)
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

        $this->postJson(
                route('trip-order.store', ['client' => $client->id]),
                self::REQUEST_DATA
            )->assertStatus(422)
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

        $this->checkResponse($response, $tripOrder);
    }

    public function testIsTripOrderSuccessfullyConfirmed(): void
    {
        $client = $this->makeAuthClient();

        $driversCount = $this->createDriversAtWork();

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

        $this->checkResponse($response, $tripOrder);

        $this->assertEquals($tripOrder->shifts()->count(), $driversCount);

        $this->assertDatabaseHas('trip_orders', ['id' => $tripOrder->id, 'status' => TripStatuses::LOOKING_FOR_DRIVER]);
    }

    public function testIsTripOrderNotFoundErrorShown(): void
    {
        $client = $this->makeAuthClient();

        $this->postJson(
                route('trip-order.confirm', ['client' => $client->id]),
                [TripOrder::PAYMENT_METHOD_ID => 'test payment method']
            )->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => TripMessages::REQUEST_NOT_FOUND
            ]);
    }

    public function testIsTripOrderSuccessfullyCanceled(): void
    {
        $client = $this->makeAuthClient();
        $this->createDriversAtWork();

        $tripOrder = factory(TripOrder::class)->create(
            [
                TripOrder::CLIENT_ID => $client->id,
                TripOrder::STATUS => $this->faker->randomElement([
                    TripStatuses::WAITING_FOR_CONFIRMATION,
                    TripStatuses::LOOKING_FOR_DRIVER,
                    TripStatuses::DRIVER_IS_ON_THE_WAY,
                    TripStatuses::DRIVER_IS_WAITING_FOR_CLIENT,
                ]),
            ]
        );

        $shifts = Shift::all();

        $tripOrder->shifts()->sync(
            $shifts->pluck(Shift::ID)->toArray()
        );

        $drivers = Driver::whereIn(Driver::ID, $shifts->pluck(Shift::DRIVER_ID)->toArray());

        Notification::fake();

        $this->postJson(
                route('trip.client-cancel', ['client' => $client->id])
            )
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => TripMessages::CANCELED,
            ]);

        foreach ($drivers as $driver) {
            Notification::assertSentTo($driver, TripOrderCanceled::class);
        }

        $this->assertDatabaseMissing('trip_orders', ['id' => $tripOrder->id]);
    }

    public function testIsTripOrderInProgressCannotBeCanceled(): void
    {
        $client = $this->makeAuthClient();

        $tripOrder = factory(TripOrder::class)->create(
            [
                TripOrder::CLIENT_ID => $client->id,
                TripOrder::STATUS => $this->faker->randomElement([
                    TripStatuses::TRIP_IN_PROGRESS,
                    TripStatuses::UNRATED,
                ]),
            ]
        );

        $this->postJson(
                route('trip.client-cancel', ['client' => $client->id])
            )->assertStatus(200)
            ->assertJson([
                'done' => true,
                'message' => TripMessages::CANNOT_BE_CANCELED,
            ]);

        $this->assertDatabaseHas('trip_orders', ['id' => $tripOrder->id]);
    }

    protected function checkResponse($response, TripOrder $tripOrder)
    {
        $encodedResource = (new TripOrderResource($tripOrder))->response()->getContent();
        $data = json_decode($encodedResource, true);

        $response
            ->assertStatus(200)
            ->assertJson([
                'done' => true,
                'data' => $data
            ]);
    }
}
