<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi CORS untuk mengizinkan Flutter Web (Chrome) mengakses
    | API dan file storage (gambar produk, avatar, dll).
    |
    */

    // Path yang akan diproses oleh middleware CORS
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'storage/*',      // ← Izinkan akses gambar dari Flutter Web
    ],

    'allowed_methods' => ['*'],

    // Untuk testing, izinkan semua origin. Nanti di production bisa dibatasi.
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
