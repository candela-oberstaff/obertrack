<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root class namespace for Livewire component classes in
    | your application. This value will change where component upgrades produce
    | new component classes.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | This value sets the path for Livewire component views. This value will
    | change where component upgrades produce new component views.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | The default layout view that will be used when rendering a component via
    | Route::get('/some-endpoint', SomeComponent::class);. In this case the
    | the view returned by SomeComponent will be wrapped in "layouts.app"
    |
    */

    'layout' => 'layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |--------------------------------------------------------------------------
    |
    | The default placeholder view that will be used when lazy loading a component.
    |
    */

    'lazy_placeholder' => null,

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire handles file uploads automatically by storing them in a temporary
    | directory before the user "saves" them. You may configure the details
    | of this temporary storage below.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,        // Example: 'local', 's3'              | Default: 'default'
        'rules' => null,       // Example: ['file', 'mimes:png,jpg']  | Default: ['required', 'file', 'max:12288'] (12MB)
        'directory' => null,   // Example: 'tmp'                      | Default: 'livewire-tmp'
        'middleware' => null,  // Example: 'throttle:5,1'             | Default: 'throttle:60,1'
        'preview_mimes' => [   // Supported file types for temporary previews...
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5, // Max duration (in minutes) before an upload gets invalidated...
    ],

    /*
    |--------------------------------------------------------------------------
    | Manifest File Path
    |--------------------------------------------------------------------------
    |
    | This value sets the path to the Livewire manifest file.
    |
    */

    'manifest_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Back-button cache
    |--------------------------------------------------------------------------
    |
    | If set to false, Livewire normally won't store the current page's state
    | in the browser's back-button cache.
    |
    */

    'back_button_cache' => false,

    /*
    |--------------------------------------------------------------------------
    | Render On Redirect
    |--------------------------------------------------------------------------
    |
    | This value determines if Livewire should render the component one last
    | time before redirecting.
    |
    */

    'render_on_redirect' => false,

    /*
    |--------------------------------------------------------------------------
    | Asset URL
    |--------------------------------------------------------------------------
    |
    | This value accepts a string or a function that returns a string. The returned
    | string will be used as the base URL for fetching Livewire's assets.
    |
    */

    'asset_url' => '/internal/livewire.js',

    /*
    |--------------------------------------------------------------------------
    | App URL
    |--------------------------------------------------------------------------
    |
    | This value accepts a string or a function that returns a string. The returned
    | string will be used as the base URL for Livewire's requests.
    |
    */

    'app_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Middleware Group
    |--------------------------------------------------------------------------
    |
    | This value sets the middleware group that Livewire uses for its routes.
    |
    */

    'middleware_group' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Injected Assets
    |--------------------------------------------------------------------------
    |
    | By default, Livewire injects its scripts and styles into your app's <head>
    | and <body>. You can disable this behavior by setting this to false.
    |
    */

    'inject_assets' => true,

    /*
    |--------------------------------------------------------------------------
    | Navigate (SPA mode)
    |--------------------------------------------------------------------------
    |
    | By default, Livewire's "wire:navigate" feature is disabled. You can
    | enable it by setting this value to true.
    |
    */

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    /*
    |--------------------------------------------------------------------------
    | Morph Markers
    |--------------------------------------------------------------------------
    |
    | Livewire automatically injects morph markers into your app's HTML. These
    | markers are used by Livewire to match DOM elements to their component
    | counterparts. You can disable this behavior by setting this to false.
    |
    */

    'inject_morph_markers' => true,

    /*
    |--------------------------------------------------------------------------
    | Pagination Theme
    |--------------------------------------------------------------------------
    |
    | This value sets the pagination theme that Livewire uses for its pagination
    | links.
    |
    */

    'pagination_theme' => 'tailwind',

];
