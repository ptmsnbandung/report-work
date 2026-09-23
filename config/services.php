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

    'vapid' => [
        'public_key' => env('VAPID_PUBLIC_KEY', 'BIeHMPvxMq1udSWe4JdN2g1VhHx5R9pApaXmIdOQ25P0iZ4C09x6V8FOpQepayrNobG6TCopfEli8YJ6GPEu6R4'),
        'private_key' => env('VAPID_PRIVATE_KEY', 'jcRK7F2XSJWbwM1PgFTgx2pouBnwxQDa1rt39Zc6rSU'),
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@ptmsn.co.id'),
    ],

    'whatsapp' => [
        'api_url' => env('WA_API_URL', 'https://api.fonnte.com/send'),
        'api_key' => env('WA_API_KEY', ''),
        'simulate' => env('WA_SIMULATE', true),
    ],

];
