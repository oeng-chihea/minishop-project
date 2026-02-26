<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ABA PayWay Configuration
    |--------------------------------------------------------------------------
    */

    'merchant_id'   => env('ABA_MERCHANT_ID'),
    'api_key'       => env('ABA_API_KEY'),
    'api_key_valid_until' => env('ABA_API_KEY_VALID_UNTIL'),
    'checkout_url'  => env('ABA_PAYWAY_CHECKOUT_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase'),
    'api_url'       => env('ABA_PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase'),
    'currency'      => env('ABA_CURRENCY', 'USD'),
    'type'          => env('ABA_TRANSACTION_TYPE', 'purchase'),
    'payment_option'=> env('ABA_PAYMENT_OPTION', ''),
    'skip_success_page' => env('ABA_SKIP_SUCCESS_PAGE', '1'),
    'lifetime'      => env('ABA_PAYMENT_LIFETIME', '30'),

    // `return_url` is PayWay pushback callback endpoint (POST JSON).
    'return_url'         => env('ABA_PAYWAY_RETURN_URL', env('APP_URL') . '/api/payment/webhook'),
    'cancel_url'         => env('ABA_PAYWAY_CANCEL_URL', env('APP_URL') . '/payment/cancel'),
    'continue_success_url' => env('ABA_PAYWAY_CONTINUE_SUCCESS_URL', env('APP_URL') . '/payment/result'),
];
