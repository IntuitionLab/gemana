<?php

use Laravel\Fortify\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Fortify Guard
    |--------------------------------------------------------------------------
    */
    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Fortify Password Broker
    |--------------------------------------------------------------------------
    */
    'passwords' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Username / Email
    |--------------------------------------------------------------------------
    */
    'username' => 'email',
    'email' => 'email',

    /*
    |--------------------------------------------------------------------------
    | Home Path
    |--------------------------------------------------------------------------
    | Where to redirect after login. Our middleware will override this
    | per-role (super-admin → /admin, member → /portal).
    */
    'home' => '/portal/dashboard',

    /*
    |--------------------------------------------------------------------------
    | Prefix & Middleware
    |--------------------------------------------------------------------------
    */
    'prefix' => '',
    'domain' => null,
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Register View
    |--------------------------------------------------------------------------
    */
    'views' => true,

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    | Enabled features for Gemana:
    |   - Registration        → public self-signup
    |   - ResetPasswords      → forgot password flow
    |   - EmailVerification   → required before portal access
    |   - UpdateProfileInformation
    |   - UpdatePasswords
    |   - TwoFactorAuthentication → TOTP + recovery codes
    */
    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirm' => true,   // Must confirm TOTP code after enabling
            'confirmPassword' => true, // Must re-enter password before enabling/disabling 2FA
        ]),
    ],

];
