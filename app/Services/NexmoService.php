<?php

namespace App\Services;

use App\Constants\NexmoMessages;
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
        if (config('app.env') === 'dev') {
            return ['sent' => true, 'message' => 'Skip SMS sending on dev environment'];
        }

        try {
            $from = config('nexmo.from');
            $to = $this->redirectDebugNumber($to);
            $to = preg_replace("/[^0-9^+]/", '', $to);

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
                ? NexmoMessages::SUCCESS
                : sprintf(NexmoMessages::FAILED_WITH_STATUS, $response);
        } catch (Exception $e) {
            $statusMessage = sprintf(NexmoMessages::FAILED_WITH_ERROR, $e->getMessage());
            $messageSent = false;
        }

        $this->log($statusMessage);

        return ['sent' => $messageSent, 'message' => $statusMessage];
    }

    protected function redirectDebugNumber(string $to): string
    {
        if ($to === '+1 (999) 999-9999') {
            return '+380675393904';
        }

        return $to;
    }

    protected function log(string $message): void
    {
        Log::channel('nexmo')->info($message);
    }
}
