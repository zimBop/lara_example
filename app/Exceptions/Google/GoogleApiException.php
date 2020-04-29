<?php

namespace App\Exceptions\Google;

use Symfony\Component\HttpKernel\Exception\HttpException;

class GoogleApiException extends HttpException
{
    public function __construct(array $response)
    {
        $message = $response['error_message'] ?? $response['status'];

        parent::__construct(422, $message);
    }
}
