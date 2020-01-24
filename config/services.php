<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    
    'flock_logs' => [
        'enabled' => env('FLOCK_LOGS_ENABLED', 0),
        'uri' => env('FLOCK_LOGS_URI', 'https://api.flock.com/hooks/sendMessage/a2c7bf89-fc85-4d56-b59b-aa4d4224318c'),
        'timeout' => env('FLOCK_LOGS_TIMEOUT', 0.5),
    ],
    
    'prod_ssl_enabled' => env('SSL_ENABLED', true),

];
