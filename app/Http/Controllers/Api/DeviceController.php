<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Device\DeviceRequest;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;

class DeviceController extends ApiController
{
    /**
     * @param DeviceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DeviceRequest $request)
    {
        $user = Auth::user();

        $user->devices()->create($request->input());

        return $this->done('Device successfully added.');
    }

    /**
     * @param DeviceRequest $request
     */
    public function destroy(DeviceRequest $request)
    {
        $user = Auth::user();

        $deleted = $user->devices()->where(
            [
                Device::TOKEN => $request->input(Device::TOKEN),
                Device::TYPE => $request->input(Device::TYPE),
            ]
        )->delete();

        return $this->done(
            $deleted ? 'Device successfully deleted.' : 'Device not found.'
        );
    }
}
