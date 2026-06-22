<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Development login routes (/test-accounts, /test-login)
    |--------------------------------------------------------------------------
    |
    | Disabled by default in production. Set ENABLE_DEV_LOGIN=true to allow
    | one-click test logins (local/staging only).
    |
    */
    'dev_login' => [
        'enabled' => filter_var(
            env('ENABLE_DEV_LOGIN', env('APP_ENV') === 'local'),
            FILTER_VALIDATE_BOOL
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */
    'attendance' => [
        'late_after' => env('ATTENDANCE_LATE_AFTER', '09:00'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subdirectory deployment
    |--------------------------------------------------------------------------
    |
    | When APP_URL includes a path (e.g. https://cbp.africacdc.org/warcc), do not
    | run `php artisan route:cache` — it breaks the homepage with HTTP 405.
    |
    */
    'subdirectory' => env('APP_URL') && (bool) parse_url(env('APP_URL'), PHP_URL_PATH),

];
