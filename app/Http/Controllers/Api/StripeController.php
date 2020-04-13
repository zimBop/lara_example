<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripeController extends ApiController
{
    public function getEphemeralKey(Client $client, StripeService $stripeService)
    {
        $stripeService->setClient($client);

        return $this->data(
            $stripeService->getEphemeralKey()
        );
    }
}
