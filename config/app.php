<?php

return [
    'name' => env('APP_NAME', 'Nazeefa CommerceOS'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'https://nazeefa.com'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Dhaka'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY', 'base64:XG8d2Lq3N8Z9F0A1b2C3D4e5F6g7H8i9J0K1L2M3N4O='),
    'previous_keys' => [],
    'maintenance' => [
        'driver' => 'file',
    ],
];
