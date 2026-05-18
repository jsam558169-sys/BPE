<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    | Changed from 'web' to 'borrower' so the default login goes to borrowers.
    | Admin login uses the 'admin' guard explicitly.
    */

    'defaults' => [
        'guard'     => 'borrower',
        'passwords' => 'borrowers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        // Borrower (Faculty) guard — uses the borrowers table
        'borrower' => [
            'driver'   => 'session',
            'provider' => 'borrowers',
        ],

        // Admin guard — uses the admins table
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'borrowers' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Borrower::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'borrowers' => [
            'provider' => 'borrowers',
            'table'    => 'password_reset_tokens_borrowers',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table'    => 'password_reset_tokens_admins',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
