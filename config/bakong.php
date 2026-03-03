<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bakong KHQR Configuration
    |--------------------------------------------------------------------------
    */

    // Your Bakong account ID (phone@bankcode), e.g. 0501063085@abaa
    'account_id'    => env('BAKONG_ACCOUNT_ID'),

    // Name shown on the KHQR payment screen
    'merchant_name' => env('BAKONG_MERCHANT_NAME', 'Mini Shop KH'),

    // City shown on the KHQR payment screen
    'merchant_city' => env('BAKONG_MERCHANT_CITY', 'PHNOM PENH'),

    // API token from api-bakong.nbc.gov.kh (JWT)
    'token'         => env('BAKONG_TOKEN'),

    // Currency: USD or KHR
    'currency'      => env('BAKONG_CURRENCY', 'USD'),

    // 'sandbox' uses Bakong SIT (test) API; 'production' uses live API
    'environment'   => env('BAKONG_ENV', 'production'),

    // How many seconds before the generated QR expires (max: 1800)
    'qr_lifetime'   => (int) env('BAKONG_QR_LIFETIME', 300),
];
