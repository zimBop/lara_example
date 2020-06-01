<?php

namespace Tests\Feature\Traits;

use App\Models\City;
use App\Models\Driver;
use App\Models\DriverLocation;
use App\Models\Shift;
use GoogleMaps\Directions;
use Illuminate\Support\Facades\DB;

trait TripTrait
{
    protected function createDriverAtWork(bool $isAuthDriver = false, ?string $cityName = 'Hartford', array $data = []): Driver
    {
        if ($isAuthDriver) {
            $driver = $this->makeAuthDriver();
        } else {
            $driver = factory(Driver::class)->create($data);
        }

        if ($cityName) {
            $city = City::withCoordinates()->whereName($cityName)->first();
        } else {
            $city = City::withCoordinates()->first();
        }
        $shift = factory(Shift::class)->create([
            Shift::DRIVER_ID => $driver->id,
            Shift::CITY_ID => $city->id,
        ]);

        $lng = $city->longitude;
        $lat = $city->latitude;

        DriverLocation::create(
            [DriverLocation::SHIFT_ID => $shift->id],
            [DriverLocation::LOCATION => DB::raw("ST_GeometryFromText('POINT($lng $lat)', 4326)")]
        );

        return $driver;
    }

    protected function createDriversAtWork()
    {
        $count = $this->faker->numberBetween(1, 3);

        for ($i = 1; $i <= $count; $i++) {
            $this->createDriverAtWork();
        }

        return $count;
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

        $clientDirectionsApiResponse = file_get_contents(base_path() . '/tests/Feature/TripOrder/' . $fileName);

        $driverDirectionsApiResponse = file_get_contents(base_path() . '/tests/Feature/TripOrder/driverDirectionsSuccess.json');

        $directionsMock->shouldReceive('get')
            ->andReturn($clientDirectionsApiResponse, $driverDirectionsApiResponse);

        $directionsMock->shouldReceive('setParam')
            ->andReturn($directionsMock);

        return $directionsMock;
    }
}
