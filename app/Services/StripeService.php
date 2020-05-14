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

    public function makePayment(
        Trip $trip,
        string $type,
        int $amount = null,
        string $payment_method = null
    ): string {
        $customerId = $this->getCustomerId();
        $errorData = null;

        try {
            PaymentIntent::create(
                [
                    'amount' => $amount ?: $trip->price,
                    'currency' => 'usd',
                    'payment_method' => $payment_method ?: $trip->payment_method_id,
                    'customer' => $customerId,
                    'confirm' => true,
                    'off_session' => true,
                    'metadata' => [
                        'trip_id' => $trip->id,
                        'type' => $type,
                    ],
                ]
            );
        } catch (\Stripe\Exception\CardException $e) {
            $errorData = $this->prepareErrorData($trip, $type, $e);
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Too many requests made to the API too quickly
            $errorData = $this->prepareErrorData($trip, $type, $e);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
            $errorData = $this->prepareErrorData($trip, $type, $e);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $errorData = $this->prepareErrorData($trip, $type, $e);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            $errorData = $this->prepareErrorData($trip, $type, $e);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $errorData = $this->prepareErrorData($trip, $type, $e);
        } catch (\Exception $e) {
            $errorData = ['message' => $e->getMessage()];
        }

        if ($errorData) {
            $this->log(json_encode($errorData));

            return $errorData['message'] ?? 'Stripe error';
        }

        return '';
    }

    protected function prepareErrorData(Trip $trip, string $type, $error): array
    {
        return [
            'status' => $error->getHttpStatus(),
            'type' => $error->getError()->type,
            'code' => $error->getError()->code,
            'param' => $error->getError()->param,
            'message' => $error->getError()->message,
            'trip_id' => $trip->id,
            'payment_type' => $type,
        ];
    }

    protected function log(string $message): void
    {
        Log::channel('stripe')->info($message);
    }
}
