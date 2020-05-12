<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Trip;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeService
{
    protected $client;

    public function __construct()
    {
        if (config('app.env') === 'production') {
            Stripe::setApiKey(config('services.stripe.secret_key'));
        }

        Stripe::setApiKey(config('services.stripe.test_secret_key'));
        Stripe::setApiVersion(config('services.stripe.version'));
    }

    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    protected function getCustomerId(): string
    {
        if (!$this->client->customer_id) {
            $this->createCustomer();
        }

        return $this->client->customer_id;
    }

    protected function createCustomer()
    {
        $customer = Customer::create(
            [
                'phone' => $this->client->phone,
                'name' => $this->client->full_name,
            ]
        );

        $this->client->customer_id = $customer->id;
        $this->client->save();

        return $customer;
    }

    public function getEphemeralKey()
    {
        return EphemeralKey::create(
            ['customer' => $this->getCustomerId()],
            ['stripe_version' => Stripe::getApiVersion()]
        );
    }

    public function getPaymentIntentSecret(int $amount, string $currency = 'usd'): ?string
    {
        $intent = PaymentIntent::create(
            [
                'amount' => $amount,
                'currency' => $currency,
            ]
        );

        return $intent->client_secret;
    }

    public function makePayment(Trip $trip, string $type, string $currency = 'usd')
    {
        $customerId = $this->getCustomerId();

        try {
            // Create a PaymentIntent with the order amount, currency, and saved payment method ID
            // If authentication is required or the card is declined, Stripe
            // will throw an error
            $payment_intent = PaymentIntent::create(
                [
                    'amount' => $trip->price,
                    'currency' => $currency,
                    'payment_method' => $trip->payment_method_id,
                    'customer' => $customerId,
                    'confirm' => true,
                    'off_session' => true,
                    'metadata' => [
                        'trip_id' => $trip->id,
                        'type' => $type,
                    ],
                ]
            );

            // Send public key and PaymentIntent details to client
//            return $response->withJson(
//                array('succeeded' => true, 'publicKey' => $pub_key, 'clientSecret' => $payment_intent->client_secret)
//            );
        } catch (\Stripe\Exception\CardException $err) {
            $error_code = $err->getError()->code;

            if ($error_code == 'authentication_required') {
                // Bring the customer back on-session to authenticate the purchase
                // You can do this by sending an email or app notification to let them know
                // the off-session purchase failed
                // Use the PM ID and client_secret to authenticate the purchase
                // without asking your customers to re-enter their details
                Log::info(
                    json_encode(array(
                        'error' => 'authentication_required',
                        'amount' => calculateOrderAmount(),
                        'card' => $err->getError()->payment_method->card,
                        'paymentMethod' => $err->getError()->payment_method->id,
                        'clientSecret' => $err->getError()->payment_intent->client_secret
                    ))
                );
            } else {
                if ($error_code && $err->getError()->payment_intent != null) {
                    // The card was declined for other reasons (e.g. insufficient funds)
                    // Bring the customer back on-session to ask them for a new payment method
                    Log::info(
                        json_encode(array(
                            'error' => $error_code,
                            'clientSecret' => $err->getError()->payment_intent->client_secret
                        ))
                    );
                } else {
                    Log::info('Stripe Service: Unknown error occurred');
                }
            }
        }
    }
}
