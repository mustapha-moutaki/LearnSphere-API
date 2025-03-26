<?php
return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'), // Or 'live'
    'sandbox' => [
        'client_id'         => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
        'app_id'            => '',
    ],
    'live' => [
        'client_id'         => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
        'app_id'            => '',
    ],

    'payment_action' => 'Sale', // Or 'Authorization'
    'currency'       => 'USD',
    'notify_url'     => '', // Optional webhook URL
    'locale'         => 'en_US',
    'validate_ssl'   => true,
];