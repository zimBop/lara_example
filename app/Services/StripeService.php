<?php

namespace App\Services;

use App\Models\Client;
use Stripe\Customer;
use Stripe\SetupIntent;
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
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
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

    public function getSecret()
    {
        $setupIntent = SetupIntent::create(
            [
                'customer' => $this->getCustomerId()
            ]
        );

        return $setupIntent->client_secret;
    }
}
