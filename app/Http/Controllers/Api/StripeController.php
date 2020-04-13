<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\StripeService;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function getEphemeralKey(Client $client, StripeService $stripeService)
    {
        $stripeService->setClient($client);

        return response()->json(
            $stripeService->getEphemeralKey()
        );
    }

    public function getPaymentIntent(Client $client, StripeService $stripeService)
    {
        $stripeService->setClient($client);

        return response()->json(
            $stripeService->getPaymentIntentSecret(100)
        );
    }
}
