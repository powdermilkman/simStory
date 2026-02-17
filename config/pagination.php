<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pagination Defaults
    |--------------------------------------------------------------------------
    |
    | Default pagination sizes for different areas of the application.
    | These can be customized per-environment in your .env file.
    |
    */

    'forum' => env('PAGINATION_FORUM', 20),
    'admin' => env('PAGINATION_ADMIN', 20),
    'profile_posts' => env('PAGINATION_PROFILE_POSTS', 20),
];
