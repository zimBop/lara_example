<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'version' => '2020-03-02',
        'public_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'test_public_key' => env('STRIPE_TEST_PUBLISHABLE_KEY'),
        'test_secret_key' => env('STRIPE_TEST_SECRET_KEY'),
        'test_customer_id' => 'cus_H3ilXcI1KG3ILL',
        /*
         * Stripe tests send requests to the Stripe API and their
         * execution can be time consuming. We can skip these tests
         * setting 'skip_tests' to true.
         */
        'skip_tests' => env('STRIPE_SKIP_TESTS', true),
    ],

];
