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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'ton' => [
        'is_main' => env('IS_MAIN'),
        'base_uri_ton_center_test' => env('BASE_URI_TON_CENTER_TEST'),
        'base_uri_ton_center_main' => env('BASE_URI_TON_CENTER_MAIN'),
        'api_key_test' => env('TON_API_KEY_TEST'),
        'api_key_main' => env('TON_API_KEY_MAIN'),
        'root_usdt_test' => env('ROOT_USDT_TEST'),
        'root_usdt_main' => env('ROOT_USDT_MAIN'),
        'root_ton_wallet' => env('ROOT_TON_WALLET'),
        'ton_mnemonic' => env('TON_MNEMONIC'),
        'ton' => 'TON',
        'usdt' => 'USDT',
        'deposit' => 'DEPOSIT',
        'withdraw' => 'WITHDRAW',
        'jetton_opcode' => '7362d09c',
        'valid_currencies' => ['TON', 'USDT'],
    ]

];
