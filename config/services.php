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
    'adk' => [
        'api_url' => env('ADK_API_URL', 'http://localhost:8000'),
        'api_key' => env('ADK_API_KEY'),
        'timeout' => env('ADK_TIMEOUT', 30),
        'api_app_name' => env('ADK_API_APP_NAME'),
    ],
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
        'location' => env('GOOGLE_CLOUD_LOCATION', 'us-central1'),
    ],
];
