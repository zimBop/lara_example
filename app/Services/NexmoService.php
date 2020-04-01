<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Nexmo\Client as NexmoClient;
use Nexmo\Client\Exception\Exception;

/**
 * Class AssignmentService
 *
 * @package App\Services
 */
class NexmoService
{
    protected $client;

    /**
     * NexmoService constructor.
     *
     * @param NexmoClient $client
     */
    public function __construct(NexmoClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $to
     * @param string $text
     * @return string
     * @throws \Exception
     */
    public function sendSMS(string $to, string $text): string
    {
        try {
            $from = config('nexmo.from');

            Log::info("sendSMS:   to - {$to},   from - {$from},    message - {$text}");

            $message = $this->client->message()->send(
                [
                    'to' => $to,
                    'from' => $from,
                    'text' => $text,
                ]
            );

            $response = $message->getResponseData()['messages'][0]['status'];

            $statusMessage = $response == 0
                ? "SMS was successfully sent"
                : "SMS sending failed with status: " . $response . "\n";
        } catch (Exception $e) {
            $statusMessage = "SMS was not sent. Error: " . $e->getMessage() . "\n";
        }

        Log::info($statusMessage);

        return $statusMessage;
    }
}
