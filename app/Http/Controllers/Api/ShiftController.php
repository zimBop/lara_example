<?php

namespace App\Http\Controllers\Api;

use App\Models\Driver;
use App\Models\Shift;
use App\Models\Vehicle;

class ShiftController extends ApiController
{
    public function start(Driver $driver)
    {
        // TODO retrieve vehicle from schedule
        $vehicle = Vehicle::first() ?: factory(Vehicle::class)->create();

        if (!$driver->activeShift) {
            $driver->shifts()->create([
                Shift::STARTED_AT => now(),
                Shift::VEHICLE_ID => $vehicle->id,
            ]);
        }

        return $this->data([Shift::ID => $driver->activeShift->id]);
    }

    public function finish(Driver $driver)
    {
        if (!$driver->activeShift) {
            return $this->done('Active shifts not found.');
        }

        $driver->activeShift->update([
            Shift::FINISHED_AT => now()
        ]);

        return $this->done('Shift finished.');
    }
}
