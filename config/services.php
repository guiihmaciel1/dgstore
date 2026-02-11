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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mercadolivre' => [
        'client_id' => env('ML_CLIENT_ID'),
        'client_secret' => env('ML_CLIENT_SECRET'),
        'redirect_uri' => env('ML_REDIRECT_URI'),
    ],

    'scraper_proxy' => [
        'key' => env('SCRAPER_API_KEY'),
        'base_url' => env('SCRAPER_API_URL', 'https://api.scraperapi.com'),
    ],

    'facebook_marketplace' => [
        'default_location' => env('FB_MARKETPLACE_LOCATION', 'São José do Rio Preto'),
        'radius_km' => (int) env('FB_MARKETPLACE_RADIUS_KM', 40),
        'proxy_url' => env('FB_MARKETPLACE_PROXY_URL'),
        'proxy_secret' => env('FB_MARKETPLACE_PROXY_SECRET'),
    ],

];
