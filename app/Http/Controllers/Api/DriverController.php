<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Driver\GetStatisticsRequest;
use App\Http\Requests\Driver\ResetPasswordRequest;
use App\Http\Requests\Driver\ForgotPasswordRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Models\ScheduleWeek;
use App\Services\StatsService;
use App\Services\ScheduleService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\RefreshTokenRepository;
use App\Notifications\ForgotPassword;
use App\Services\ResetPasswordService;

class DriverController extends ApiController
{
    public function forgotPassword(ForgotPasswordRequest $request, ResetPasswordService $passwordService)
    {
        $driver = Driver::whereEmail($request->input('email'))->first();

        if (!$driver) {
            return $this->error('Driver with specified email not found.');
        }

        $passwordService->setModel($driver);
        $token = $passwordService->create();
        $driver->notify(new ForgotPassword($token));

        return $this->done('Notification sent to driver\'s email.');
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordService $passwordService)
    {
        $driver = Driver::whereEmail($request->input('email'))->first();

        if (!$driver) {
            return $this->error('Driver not found.');
        }

        $passwordService->setModel($driver);
        $passwordService->setNewPassword($request->input());

        return $this->done('Password reset successfully.');
    }

    public function logout(Driver $driver, RefreshTokenRepository $refreshTokenRepository)
    {
        foreach ($driver->tokens() as $token) {
            $token->revoke();

            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
        }

        return $this->done('Tokens revoked.');
    }

    public function show(Driver $driver)
    {
        return $this->data(new DriverResource($driver));
    }

    public function info()
    {
        $driver = Auth::user();

        return $this->data(new DriverResource($driver));
    }

    public function stats(
        GetStatisticsRequest $request,
        Driver $driver,
        StatsService $driverService,
        ScheduleService $scheduleService
    ) {
        $allWeeks = ScheduleWeek::whereHas(
                'gaps.shifts',
                static function (Builder $query) use ($driver) {
                    $query->whereDriverId($driver->id);
                }
            )
            ->where(ScheduleWeek::NUMBER, '<=', now()->weekOfYear)
            ->where(ScheduleWeek::YEAR, '<=', now()->year)
            ->with('gaps', 'gaps.shifts')
            ->orderBy(ScheduleWeek::YEAR, 'desc')
            ->orderBy(ScheduleWeek::NUMBER, 'desc')
            ->get();

        $page = $request->input('page', 1);
        $perPage = 2;
        $weeks = $allWeeks->forPage($page, $perPage);

        return $this->data(
            array_merge(
                [
                    'page' => (int) $page,
                    'pages_count' => ceil($allWeeks->count() / $perPage),
                ],
                $driverService->getDriverStats($driver, $weeks, $scheduleService)
            )
        );
    }
}
