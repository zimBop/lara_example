<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ScheduleMessages;
use App\Filters\ScheduleWeekFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetScheduleRequest;
use App\Http\Requests\Admin\UpdateScheduleRequest;
use App\Models\City;
use App\Models\Driver;
use App\Models\ScheduleWeek;
use App\Models\Vehicle;
use App\Scopes\IsNotTemplate;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Artisan;

class ScheduleController extends Controller
{
    public function index(GetScheduleRequest $request, ScheduleWeekFilter $filter, ScheduleService $scheduleService)
    {
        return view(
            'admin.schedule.index',
            array_merge(
                [
                    'week' => $scheduleService->getWeek($request, $filter),
                    'vehicles' => Vehicle::all(),
                    'drivers' => Driver::all(),
                    'cities' => City::all(),
                    'nextWeekExists' => $scheduleService->isNextWeekExists(),
                    'timeSelectOptions' => $scheduleService->getTimeSelectOptions(),
                ],
                $scheduleService->getDatesForWeekPicker($request)
            )
        );
    }

    public function template(ScheduleService $scheduleService)
    {
        $week = ScheduleWeek::template()->with('gaps', 'gaps.shifts')->first();

        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        $cities = City::all();

        $timeSelectOptions = $scheduleService->getTimeSelectOptions();

        return view('admin.schedule.index', get_defined_vars());
    }

    public function generate()
    {
        Artisan::call('db:seed', ['--class' => \ScheduleWeekSeeder::class]);

        return redirect()
            ->route(R_ADMIN_SCHEDULE, ['year' => now()->year, 'number' => now()->addWeek()->weekOfYear])
            ->with('success', ScheduleMessages::NEW_WEEK_GENERATED);
    }

    public function update(UpdateScheduleRequest $request, ScheduleService $scheduleService)
    {
        $week = ScheduleWeek::withoutGlobalScope(IsNotTemplate::class)
            ->find($request->input('week'));
        $scheduleService->updateWeekGaps($request, $week);
        $scheduleService->updateWeekShifts($request, $week);

        if ($week->is_template) {
            return redirect()
                ->route(R_ADMIN_SCHEDULE_TEMPLATE)
                ->with('success', ScheduleMessages::WEEK_TEMPLATE_UPDATED);
        }

        return redirect()
            ->route(R_ADMIN_SCHEDULE, ['year' => $week->year, 'number' => $week->number])
            ->with('success', ScheduleMessages::WEEK_UPDATED);
    }
}
