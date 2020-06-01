<?php

namespace App\Exceptions\Trip;

use App\Constants\TripMessages;

class AllDriversOfflineException extends TripException {
    public function __construct(
        int $statusCode,
        string $message = null,
        \Throwable $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct($statusCode, TripMessages::ALL_DRIVERS_OFFLINE);
    }
}
