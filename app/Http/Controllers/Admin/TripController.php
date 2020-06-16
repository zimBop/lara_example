<?php

namespace App\Http\Controllers\Admin;

use App\Constants\TripStatuses;
use App\Filters\TripsFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetTripsRequest;
use App\Models\Driver;
use App\Models\Trip;

class TripController extends Controller
{
    public function index(GetTripsRequest $request, TripsFilter $filter)
    {
        $input = $request->input();

        $drivers = Driver::all();

        $trips = Trip::filter($filter)
            ->where(Trip::STATUS, '>', TripStatuses::TRIP_IN_PROGRESS)
            ->paginate(25);

        return view('admin.trips', get_defined_vars());
    }
}
