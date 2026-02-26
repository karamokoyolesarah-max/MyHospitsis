<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CinetPay Configuration
    |--------------------------------------------------------------------------
    */

    'site_id' => env('CINETPAY_SITE_ID', ''),
    'api_key' => env('CINETPAY_API_KEY', ''),
    'webhook_secret' => env('CINETPAY_WEBHOOK_SECRET', ''),
    'test_mode' => env('CINETPAY_TEST_MODE', true),
    
    // API Endpoints
    'base_url' => env('CINETPAY_BASE_URL', 'https://api-checkout.cinetpay.com/v2'),
    'payment_url' => env('CINETPAY_PAYMENT_URL', 'https://api-checkout.cinetpay.com/v2/payment'),
    'check_url' => env('CINETPAY_CHECK_URL', 'https://api-checkout.cinetpay.com/v2/payment/check'),

    // Currency
    'currency' => env('CINETPAY_CURRENCY', 'XOF'),

    // Channels (payment methods to display)
    'channels' => ['MOBILE_MONEY', 'WALLET'],

    /*
    |--------------------------------------------------------------------------
    | Mobile Money Recipient Numbers (for manual transfers)
    |--------------------------------------------------------------------------
    */
    'recipients' => [
        'mtn' => env('PAYMENT_MTN_NUMBER', '0545217915'),
        'orange' => env('PAYMENT_ORANGE_NUMBER', '0788647369'),
        'moov' => env('PAYMENT_MOOV_NUMBER', '0102308696'),
        'wave' => env('PAYMENT_WAVE_LINK', 'https://pay.wave.com/m/M_ZhAYej-GAGBu/c/ci/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'provider' => env('SMS_PROVIDER', 'twilio'), // twilio, infobip, or null to disable
        'twilio' => [
            'sid' => env('TWILIO_SID', ''),
            'token' => env('TWILIO_TOKEN', ''),
            'from' => env('TWILIO_FROM', ''),
        ],
        'infobip' => [
            'api_key' => env('INFOBIP_API_KEY', ''),
            'base_url' => env('INFOBIP_BASE_URL', ''),
            'from' => env('INFOBIP_FROM', 'HospitSIS'),
        ],
    ],
];
