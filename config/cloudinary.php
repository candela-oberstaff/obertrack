<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings. Cloudinary is a cloud
    | service that offers a solution to a web application's entire image
    | management pipeline.
    | // Cache buster comment
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    */
    'cloud_url' => env('CLOUDINARY_URL'),

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),
    
    /**
     * Upload Preset Configuration
     */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    
    /**
     * Upload Route Configuration
     */
    'upload_route' => env('CLOUDINARY_UPLOAD_ROUTE'),

    /**
     * Upload Action Configuration
     */
    'upload_action' => env('CLOUDINARY_UPLOAD_ACTION'),
];
