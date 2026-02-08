<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Insurance Provider
    |--------------------------------------------------------------------------
    |
    | This value determines which insurance provider should be used.
    |
    */
    'provider' => env('INSURANCE_PROVIDER', \App\Services\Insurance\MockInsuranceProvider::class),

    'endpoints' => [
        'cnam' => env('INSURANCE_CNAM_URL', 'https://api.cnam.ci/v1'),
        'saham' => env('INSURANCE_SAHAM_URL', 'https://api.saham.ci/v1'),
    ],
];
