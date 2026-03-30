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
    'portal1' => [
    // Temporary hardcode
    'secret' => 'ed2a55fb0653eb0bad1b6391cb907b4c3da9f8b8af40f8063910eff927e88c7d',
    
    // Or use ternary as fallback
    // 'secret' => env('SERVICES_PORTAL1_SECRET', 'ed2a55fb0653eb0bad1b6391cb907b4c3da9f8b8af40f8063910eff927e88c7d'),
],

];
