<?php
return [
    'mpesa' => [
        'test_key' => env('MPESA_TEST_KEY'),
        'public_key' => env('MPESA_PUBLIC_KEY'), // Added this
        'prod_key' => env('MPESA_PROD_KEY'),
    ],
];
