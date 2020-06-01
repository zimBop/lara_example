<?php

namespace App\Http\Controllers\Api;

use App\Constants\DriverMessages;
use App\Http\Requests\Driver\UpdateLocationRequest;
use App\Models\City;
use App\Models\Driver;
use App\Models\DriverLocation;
use App\Models\Shift;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftController extends ApiController
{
    public function start(UpdateLocationRequest $request, Driver $driver)
    {
        // TODO retrieve vehicle and city from schedule
        $vehicle = Vehicle::first() ?: factory(Vehicle::class)->create();
        if (config('app.test_trip_flow')) {
            $city = City::whereName('Krivoy Rog')->first();
        } else {
            $city = City::first();
        }

        if (!$driver->activeShift) {
            $driver->shifts()->create([
                Shift::STARTED_AT => now(),
                Shift::VEHICLE_ID => $vehicle->id,
                Shift::CITY_ID => $city->id,
            ]);
        }

        $lng = $request->input('longitude');
        $lat = $request->input('latitude');

        DriverLocation::updateOrCreate(
            [DriverLocation::SHIFT_ID => $driver->activeShift->id],
            [DriverLocation::LOCATION => DB::raw("ST_GeometryFromText('POINT($lng $lat)', 4326)")]
        );

        return $this->data([Shift::ID => $driver->activeShift->id]);
    }

    public function finish(Driver $driver)
    {
        if ($driver->activeShift->active_trip) {
            return $this->error(DriverMessages::CANNOT_STOP_ACTIVE_TRIP);
        }

        if (!$driver->activeShift) {
            return $this->done(DriverMessages::SHIFT_NOT_FOUND);
        }

        $driver->activeShift->driver_location->delete();
        $driver->activeShift->trip_orders()->detach();

        $driver->activeShift->update([
            Shift::FINISHED_AT => now()
        ]);

        return $this->done(DriverMessages::SHIFT_FINISHED);
    }

    public function location(UpdateLocationRequest $request, Driver $driver)
    {
        if (!$driver->activeShift) {
            return $this->done(DriverMessages::SHIFT_NOT_FOUND);
        }

        $lng = $request->input('longitude');
        $lat = $request->input('latitude');

        $msg = "Driver ID: " . $driver->id . " || Coords: $lng, $lat";

        Log::channel('driver_locations')->info($msg);

        DriverLocation::updateOrCreate(
            [DriverLocation::SHIFT_ID => $driver->activeShift->id],
            [DriverLocation::LOCATION => DB::raw("ST_GeometryFromText('POINT($lng $lat)', 4326)")]
        );

        return $this->done(DriverMessages::LOCATION_UPDATED);
    }

    public function washVehicle(Driver $driver)
    {
        $driver->active_shift->update([
            Shift::WASHED_AT => now()
        ]);

        return $this->done(DriverMessages::VEHICLE_WASHED);
    }
}
