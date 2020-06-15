<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Shift;

class ShiftService
{
    /**
     * @param Driver $driver
     * @throws \Exception
     */
    public function finishPending(Driver $driver): void
    {
        $pendingShifts = $driver->shifts()->pending()->get();

        foreach ($pendingShifts as $shift) {
            $this->finish($shift);
        }
    }

    /**
     * @param Driver $driver
     * @throws \Exception
     */
    public function finishAll(Driver $driver): void
    {
        $shifts = $driver->shifts()
            ->whereNull(Shift::FINISHED_AT)
            ->get();

        foreach ($shifts as $shift) {
            $this->finish($shift);
        }
    }

    /**
     * @param Shift $shift
     * @throws \Exception
     */
    public function finish(Shift $shift): void
    {
        if ($shift->driver_location) {
            $shift->driver_location->delete();
        }

        $shift->trip_orders()->detach();

        $shift->update([
            Shift::FINISHED_AT => now()
        ]);
    }
}
