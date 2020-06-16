<?php

namespace App\Http\Controllers\Admin;

use App\Constants\TripStatuses;
use App\Http\Controllers\Controller;
use App\Models\Trip;

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
    public function index()
    {
        $trips = Trip::where(Trip::STATUS, '>', TripStatuses::TRIP_IN_PROGRESS)
            ->whereDate(Trip::UPDATED_AT, '=', now())
            ->get();

        $earned = $trips->reduce(function($earned, $trip) {
            return [
                'priceSum' => $earned['priceSum'] + $trip->price,
                'tipsSum' => $earned['tipsSum'] + ($trip->tips ? $trip->tips->amount : 0),
            ];
        }, ['priceSum' => 0, 'tipsSum' => 0]);

        $tripsCount = $trips->count();

        return view('admin.dashboard', compact('earned', 'tripsCount'));
    }
}
