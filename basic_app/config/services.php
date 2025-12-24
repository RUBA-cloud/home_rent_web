<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file stores credentials for third-party services such as Mailgun,
    | Postmark, AWS, Slack, etc. Packages can reference these values via
    | the conventional "services" config.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token'   => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'                => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // ✅ Firebase Web (top-level, not inside "slack")
    'firebase_web' => [
        'api_key'              => env('FIREBASE_API_KEY'),
        'auth_domain'          => env('FIREBASE_AUTH_DOMAIN'),
        'project_id'           => env('FIREBASE_PROJECT_ID'),
        'storage_bucket'       => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id'  => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id'               => env('FIREBASE_APP_ID'),
        'vapid_key'            => env('FIREBASE_VAPID_KEY'),
    ],

    // ✅ JWT config (use in middleware: config('services.jwt.secret'))
    'jwt' => [
        'secret' => env('JWT_SECRET', ''),    // set in .env
        'algo'   => env('JWT_ALGO', 'HS256'), // optional; default HS256
    ],
'twilio' => [
        'sid'       => env('TWILIO_SID'),
        'token'     => env('TWILIO_TOKEN'),
        'verify_sid'=> env('TWILIO_VERIFY_SID'),
    ],
];
