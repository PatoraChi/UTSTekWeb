<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary URL
    |--------------------------------------------------------------------------
    |
    | Jika CLOUDINARY_URL di .env diisi, ini akan dipakai.
    | Jika tidak, 'cloud' array di bawah akan digunakan.
    |
    */
    'cloud_url' => env('CLOUDINARY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Cloud (INI YANG HILANG)
    |--------------------------------------------------------------------------
    |
    | Ini adalah array 'cloud' yang dicari oleh Service Provider.
    | Ini akan membaca variabel .env Anda dengan BENAR.
    |
    */
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key'    => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Tambahan
    |--------------------------------------------------------------------------
    */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    'upload_route' => env('CLOUDINARY_UPLOAD_ROUTE'),
    'upload_action' => env('CLOUDINARY_UPLOAD_ACTION'),

];