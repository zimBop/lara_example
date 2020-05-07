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
         */
        'certificate' => __DIR__ . '/iosCertificates/'
            . (env('APP_ENV') === 'production' ? 'apns.pem' : 'apns-dev.pem'),
        'passPhrase' => '1234', //Optional
//        'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run' => env('APP_ENV') !== 'production',
    ],
];
