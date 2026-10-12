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

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash-lite'),
        'vision_model' => env('GEMINI_VISION_MODEL', 'gemini-2.5-flash'),
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v21.0'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
    ],

    'uazapi' => [
        'base_url' => env('UAZAPI_BASE_URL', 'https://dgstore.uazapi.com'),
        'instance_token' => env('UAZAPI_INSTANCE_TOKEN'),
        'admin_token' => env('UAZAPI_ADMIN_TOKEN'),
        'store_pix_type' => env('STORE_PIX_TYPE', 'PHONE'),
        'store_pix_key' => env('STORE_PIX_KEY'),
        'store_pix_name' => env('STORE_PIX_NAME', 'DG Store'),
        'store_latitude' => env('STORE_LATITUDE'),
        'store_longitude' => env('STORE_LONGITUDE'),
        'store_name' => env('STORE_NAME', 'DG Store'),
        'store_address' => env('STORE_ADDRESS'),
    ],

    'dgifipe' => [
        'base_url' => env('DGIFIPE_API_URL', 'https://ifipe.dgstorerp.com.br'),
        'token' => env('DGIFIPE_API_TOKEN'),
        'timeout' => 10,
    ],

];
