<?php
/**
 * @see https://github.com/Edujugon/PushNotification
 */

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'My_ApiKey',
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'My_ApiKey',
    ],
    'apn' => [
        /*
         * @see https://github.com/Edujugon/PushNotification/wiki/APNS-Certificate
         *
         * Note: on step 2 '-in' parameter should be 'apns-dev-cert.p12' in case 'apns-dev-key.p12' is not exists
         *
         */
        'certificate' => __DIR__ . '/iosCertificates/'
            . (env('APP_ENV') === 'production' ? 'apns.pem' : 'apns-dev.pem'),
        'passPhrase' => '1234', //Optional
//        'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run' => env('APP_ENV') !== 'production',
    ],
];
