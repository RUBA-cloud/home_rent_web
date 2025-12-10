<?php

$cluster = env('PUSHER_APP_CLUSTER', 'ap2');
$host    = env('PUSHER_HOST');                 // leave empty for Pusher.com
$port    = env('PUSHER_PORT', 443);            // 443 for Cloud, 6001 for self-hosted
$scheme  = env('PUSHER_SCHEME', 'https');      // https for Cloud, http for self-hosted
$useTLS  = $scheme === 'https';

$pusherOptions = [
    'cluster' => $cluster,
    'useTLS'  => $useTLS,
];

// only override host/port/scheme if you're self-hosting (Reverb / laravel-websockets)
if (!empty($host)) {
    $pusherOptions = array_merge($pusherOptions, [
        'host'   => $host,
        'port'   => (int) $port,
        'scheme' => $scheme,
    ]);
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    */
    'default' => env('BROADCAST_CONNECTION', 'pusher'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key'    => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => $pusherOptions,

            // Optional Guzzle options (REST requests)
            'client_options' => [
                // 'timeout' => 2.0,
                // 'verify' => true,
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key'    => env('ABLY_KEY'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
