<?php

namespace Tests\Unit;

use App\Constants\TripStatuses;
use App\Exceptions\Trip\AllDriversOfflineException;
use App\Exceptions\Trip\ClientsQueueIsFullException;
use App\Models\City;
use App\Models\Shift;
use App\Models\Trip;
use App\Models\TripOrder;
use App\Services\TripService;
use Tests\TestCase;
use Tests\Feature\Traits\TripTrait;

class TripServiceTest extends TestCase
{
    use TripTrait;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIsClientsQueueFull()
    {
        $tripOrders = factory(TripOrder::class, $this->createDriversAtWork())->create();

        foreach ($tripOrders as $order) {
            $shift = Shift::inRandomOrder()->first();
            $order->shifts()->attach($shift->id);
        }

        /* @var  $tripService \App\Services\TripService */
        $tripService = $this->app->make(TripService::class);

        $cityId = City::whereName('Hartford')->first()->id;
        $activeShiftsNumber = Shift::active()->where(Shift::CITY_ID, $cityId)->count();

        $this->assertTrue($tripService->isClientsQueueFull($cityId, $activeShiftsNumber));

        $tripOrders->first()->shifts()->detach();

        $this->assertFalse($tripService->isClientsQueueFull($cityId, $activeShiftsNumber));
    }

    public function testFindDrivers()
    {
        $this->createDriverAtWork(false, 'New Haven');

        $driversInHartfordCount = $this->createDriversAtWork();

        /* @var  $tripService \App\Services\TripService */
        $tripService = $this->app->make(TripService::class);

        $city = City::withCoordinates()->whereName('Hartford')->first();

        $foundDrivers = $tripService->findDrivers($city->longitude, $city->latitude);

        $this->assertEquals($driversInHartfordCount, $foundDrivers->count());
    }

    public function testIsAllDriversOfflineExceptionThrown()
    {
        $this->expectException(AllDriversOfflineException::class);

        /* @var  $tripService \App\Services\TripService */
        $tripService = $this->app->make(TripService::class);

        $city = City::withCoordinates()->whereName('Hartford')->first();

        $tripService->findDrivers($city->longitude,$city->latitude);
    }

    public function testIsClientsQueueIsFullExceptionThrown()
    {
        $this->createDriversAtWork();
        $shifts = Shift::all();

        foreach ($shifts as $shift) {
            $tripOrder = factory(TripOrder::class)->create();
            $tripOrder->shifts()->attach($shift->id);
            factory(Trip::class)->create([
                Trip::SHIFT_ID => $shift->id,
                Trip::STATUS => TripStatuses::DRIVER_IS_ON_THE_WAY
            ]);
        }

        /* @var  $tripService \App\Services\TripService */
        $tripService = $this->app->make(TripService::class);

        $city = City::withCoordinates()->whereName('Hartford')->first();

        $this->expectException(ClientsQueueIsFullException::class);

        $tripService->findDrivers($city->longitude, $city->latitude);
    }
}
