<?php

return [
    'mode' => env('PAYMENT_MODE', 'sandbox'),

    'gateways' => [
        'mellat' => [
            'terminal_id' => env('MELLAT_TERMINAL_ID'),
            'username' => env('MELLAT_USERNAME'),
            'password' => env('MELLAT_PASSWORD'),
        ],
        'parsian' => [
            'pin' => env('PARSIAN_PIN'),
        ],
        'digipay' => [
            'client_id' => env('DIGIPAY_CLIENT_ID'),
            'client_secret' => env('DIGIPAY_CLIENT_SECRET'),
        ],
        'snapppay' => [
            'merchant_id' => env('SNAPPPAY_MERCHANT_ID'),
            'secret' => env('SNAPPPAY_SECRET'),
        ],
    ],
];
