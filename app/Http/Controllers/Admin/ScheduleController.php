<?php

namespace App\Http\Controllers\Admin;

use App\Filters\ScheduleWeekFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetScheduleRequest;
use App\Http\Requests\Admin\UpdateScheduleRequest;
use App\Models\City;
use App\Models\Driver;
use App\Models\ScheduleWeek;
use App\Models\Vehicle;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Artisan;

class ScheduleController extends Controller
{
    public function index(GetScheduleRequest $request, ScheduleWeekFilter $filter, ScheduleService $scheduleService)
    {
        $firstWeek = $scheduleService->getFirstWeek();

        $currentYear = now()->year;
        $years = $firstWeek ? range($firstWeek->year, $currentYear) : [$currentYear];
        $selectedYear = $request->input('year', $currentYear);

        $currentWeek = now()->weekOfYear;
        $selectedWeek = $request->input('number', $currentWeek);

        if ($request->has('year') && $request->has('number')) {
            $week = ScheduleWeek::filter($filter)->first();
        } else {
            $week = ScheduleWeek::current()->first();
        }
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        $cities = City::all();

        $nextWeekExists = $scheduleService->isNextWeekExists();
        $timeSelectOptions = $scheduleService->getTimeSelectOptions();

        return view('admin.schedule.index', get_defined_vars());
    }

    public function generate()
    {
        Artisan::call('db:seed', ['--class' => \ScheduleWeekSeeder::class]);
        $generatedWeek = now()->addWeek()->weekOfYear;

        return redirect()
            ->route(R_ADMIN_SCHEDULE, ['year' => now()->year, 'number' => $generatedWeek])
            ->with('success', 'New schedule week successfully generated.');
    }

    public function update(UpdateScheduleRequest $request, ScheduleWeek $week, ScheduleService $scheduleService)
    {
        $scheduleService->updateWeekGaps($request, $week);
        $scheduleService->updateWeekShifts($request, $week);

        return redirect()
            ->route(R_ADMIN_SCHEDULE, ['year' => $week->year, 'number' => $week->number])
            ->with('success', 'Schedule week successfully updated.');
    }
}
