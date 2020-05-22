<?php

namespace Tests\Feature\Traits;

use App\Constants\NexmoMessages;
use App\Services\NexmoService;
use Mockery;

trait Nexmo
{
    protected function createNexmoMock()
    {
        $nexmoMock = Mockery::mock(NexmoService::class);
        $this->app->instance(NexmoService::class, $nexmoMock);

        return $nexmoMock;
    }

    protected function setupSmsSendingWithError($nexmoMock, string $error): void
    {
        $nexmoMock->shouldReceive('sendSMS')
            ->once()
            ->andReturn(['sent' => false, 'message' => $error]);
    }

    protected function setupSuccessfulSmsSending($nexmoMock): void
    {
        $nexmoMock->shouldReceive('sendSMS')
            ->once()
            ->andReturn(['sent' => true, 'message' => NexmoMessages::SUCCESS]);
    }
}
