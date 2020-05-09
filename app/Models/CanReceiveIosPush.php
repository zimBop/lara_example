<?php

namespace App\Models;

use App\Constants\DeviceType;

trait CanReceiveIosPush
{
    /**
     * Route notifications for the Apn channel.
     *
     * @return string|array
     */
    public function routeNotificationForApn()
    {
        $devices = $this->devices()->whereType(DeviceType::IOS);

        if (!$devices) {
            return [];
        }

        return $devices->pluck(Device::TOKEN)->toArray();
    }

}
