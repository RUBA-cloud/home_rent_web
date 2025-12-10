<?php

return [

    // ✅ Make "log" the default mailer (falls back to 'log' if env is missing)
    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => ['transport' => 'ses'],
        'postmark' => ['transport' => 'postmark'],
        'resend' => ['transport' => 'resend'],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        // ✅ Log mailer
        'log' => [
            'transport' => 'log',
            // If MAIL_LOG_CHANNEL not set, fall back to app log channel (LOG_CHANNEL) or 'stack'
            'channel' => env('MAIL_LOG_CHANNEL', env('LOG_CHANNEL', 'stack')),
        ],

        'array' => ['transport' => 'array'],

        // Optional mailers; you won’t use these while default is "log"
        'failover' => [
            'transport' => 'failover',
            'mailers' => ['smtp', 'log'],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => ['ses', 'postmark'],
            'retry_after' => 60,
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'no-reply@example.test'),
        'name' => env('MAIL_FROM_NAME', 'Laravel'),
    ],
];
