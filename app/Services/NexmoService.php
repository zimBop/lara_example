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
     * @return array
     * @throws \Exception
     */
    public function sendSMS(string $to, string $text): array
    {
        try {
            $from = config('nexmo.from');
            $to = $this->redirectDebugNumber($to);

            $this->log("sendSMS:   to - {$to},   from - {$from},    message - {$text}");

            $message = $this->client->message()->send(
                [
                    'to' => $to,
                    'from' => $from,
                    'text' => $text,
                ]
            );

            $response = $message->getResponseData()['messages'][0]['status'];

            $messageSent = $response == 0;

            $statusMessage = $messageSent
                ? "SMS was successfully sent"
                : "SMS sending failed with status: " . $response . "\n";
        } catch (Exception $e) {
            $statusMessage = "SMS was not sent - " . $e->getMessage() . "\n";
            $messageSent = false;
        }

        $this->log($statusMessage);

        return ['sent' => $messageSent, 'message' => $statusMessage];
    }

    protected function redirectDebugNumber(string $to): string
    {
        if ($to === '+1 (999) 999 9999') {
            return '+380 67 539 3904';
        }

        return $to;
    }

    protected function log(string $message): void
    {
        Log::channel('nexmo')->info($message);
    }
}
