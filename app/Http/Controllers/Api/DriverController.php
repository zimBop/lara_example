<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Driver\ResetPasswordRequest;
use App\Http\Requests\Driver\ForgotPasswordRequest;
use App\Models\Driver;
use App\Models\PasswordResetToken;
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
}
