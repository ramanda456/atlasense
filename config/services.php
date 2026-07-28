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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | External API Credentials & Base URLs (AtlaSense)
    |--------------------------------------------------------------------------
    */
    'gnews' => [
        'key' => env('GNEWS_API_KEY'),
        'url' => env('GNEWS_URL', 'https://gnews.io/api/v4'),
    ],

    'rest_countries' => [
        'key' => env('REST_COUNTRIES_API_KEY'),
        'url' => env('REST_COUNTRIES_URL', 'https://api.restcountries.com/countries/v5'),
        'legacy_url' => env('REST_COUNTRIES_LEGACY_URL', 'https://restcountries.com/v3.1'),
    ],

    'world_bank' => [
        'url' => env('WORLD_BANK_URL', 'https://api.worldbank.org/v2')
    ],

    'open_meteo' => [
        'url' => env('OPEN_METEO_URL', 'https://api.open-meteo.com/v1')
    ],

    'exchange_rate' => [
        'url' => env('EXCHANGE_RATE_URL', 'https://open.er-api.com/v6')
    ],

    'frankfurter' => [
        'url' => env('FRANKFURTER_URL', 'https://api.frankfurter.dev/v2')
    ],

];
