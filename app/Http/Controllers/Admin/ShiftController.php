<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Services\ShiftService;

class ShiftController extends Controller
{
    public function shifts()
    {
        $shifts = Shift::active()->paginate(25);

        return view('admin.shifts', get_defined_vars());
    }

    public function finish(Shift $shift, ShiftService $shiftService)
    {
        $shiftService->finish($shift);
        $driver = $shift->driver;

        return redirect()
            ->route(R_ADMIN_SHIFTS)
            ->with('success', $driver->full_name . '\'s shift successfully finished');
    }
}
