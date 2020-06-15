<?php

namespace App\Http\Controllers\Admin;

use App\Filters\ScheduleWeekFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetScheduleRequest;
use App\Models\Driver;
use App\Services\DriverService;
use App\Services\ScheduleService;

class ReportController extends Controller
{
    public function index(GetScheduleRequest $request, ScheduleService $scheduleService)
    {
        return view(
            'admin.reports.weekly_report',
            $scheduleService->getDatesForWeekPicker($request)
        );
    }

    public function download(
        GetScheduleRequest $request,
        ScheduleWeekFilter $filter,
        ScheduleService $scheduleService,
        DriverService $driverService
    )
    {
        $week = $scheduleService->getWeek($request, $filter);

        $data = Driver::all()->mapWithKeys(
            static function ($driver) use ($scheduleService, $driverService, $week) {
                $scheduleShifts = $week->shifts()
                    ->whereDriverId($driver->id)
                    ->get();

                return [
                    $driver->full_name => [
                        'hours' => $scheduleService->getWorkHours($scheduleShifts),
                        'tips' => $driverService->getTips($scheduleShifts)
                    ]
                ];
            }
        );

        return response()->streamDownload(
            static function () use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Name', 'Hours', 'Tips']);

                foreach ($data as $driverName => $driverInfo) {
                    fputcsv($file, [$driverName, $driverInfo['hours'], $driverInfo['tips']]);
                }
                fclose($file);
            },
            $scheduleService->getReportFileName($week),
            ['Content-type' => 'application/csv']
        );
    }
}
