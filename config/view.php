<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Paths
    |--------------------------------------------------------------------------
    |
    | Most applications typically only need a single path for loading their
    | views. Of course, you may have multiple paths that contain views and
    | Laravel will check each of the paths for a matching view file.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. The default shared path on STRATO kept
    | serving stale compiled views, so production now uses a fresh folder.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views-v2')) ?: storage_path('framework/views-v2')
    ),

];
