<?php

namespace App\Http\Controllers\Admin;

use App\Constants\TripStatuses;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Services\StatsService;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(StatsService $statsService)
    {
        $trips = Trip::where(Trip::STATUS, '>', TripStatuses::TRIP_IN_PROGRESS)
            ->whereDate(Trip::UPDATED_AT, '=', now())
            ->get();

        $stats = $statsService->getTripsStats($trips);

        return view('admin.dashboard', compact('stats'));
    }
}
