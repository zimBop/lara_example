<?php

namespace App\Http\Controllers\Admin;

use App\Filters\ScheduleWeekFilter;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Driver;
use App\Models\ScheduleWeek;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request, ScheduleWeekFilter $filter)
    {
        $startYear = ScheduleWeek::orderBy(ScheduleWeek::YEAR)->first()->year;

        $currentYear = now()->year;
        $years = range($startYear, $currentYear);

        $year = $request->input('year', $currentYear);
        // TODO move to ScheduleService
        $months = array_map(
            function ($index) use ($year) {
                $now = Carbon::createFromFormat('Y', $year);
                $date = $now->startOfYear()->addMonths($index);
                $monthName = $date->monthName;
                $weeks = [$date->weekOfYear => $date->startOfWeek()->format('m/d/Y') . ' - ' . $date->endOfWeek()->format('m/d/Y')];
                while ($date->monthName === $monthName) {
                    $date->addWeek();
                    $weeks[$date->weekOfYear] = $date->startOfWeek()->format('m/d/Y') . ' - ' . $date->endOfWeek()->format('m/d/Y');
                }

                return [
                    'name' => $monthName,
                    'weeks' => $weeks,
                ];
            },
            array_flip(range(1,12))
        );

        $week = ScheduleWeek::current()->first();
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        $cities = City::all();

        return view('admin.schedule.index', get_defined_vars());
    }
}
