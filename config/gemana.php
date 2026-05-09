<?php

/**
 * config/gemana.php
 *
 * Core Gemana configuration — combines platform settings (Phase 1)
 * and organisation settings (Phase 3).
 *
 * Organisation settings will be replaced by database-driven values
 * once the Organisation Settings module is built (see backlog).
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Prefix
    |--------------------------------------------------------------------------
    | The URL prefix for the Super-Admin dashboard.
    | Change this to 'gemana-admin' for extra obscurity if desired.
    |--------------------------------------------------------------------------
    */
    'admin_prefix' => env('GEMANA_ADMIN_PREFIX', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Active Theme
    |--------------------------------------------------------------------------
    */
    'active_theme' => env('GEMANA_THEME', 'gemana-default'),

    /*
    |--------------------------------------------------------------------------
    | Organisation Name
    |--------------------------------------------------------------------------
    */
    'org_name' => env('GEMANA_ORG_NAME', 'Gemana'),

    /*
    |--------------------------------------------------------------------------
    | ABN (Australian Business Number)
    | Format: XX XXX XXX XXX
    |--------------------------------------------------------------------------
    */
    'abn' => env('GEMANA_ABN', null),

    /*
    |--------------------------------------------------------------------------
    | DGR Status
    | Set to true if this organisation is a Deductible Gift Recipient.
    | This affects the wording on tax receipts.
    |--------------------------------------------------------------------------
    */
    'is_dgr' => env('GEMANA_IS_DGR', false),

    /*
    |--------------------------------------------------------------------------
    | Organisation Address
    | Displayed on tax receipts and emails.
    |--------------------------------------------------------------------------
    */
    'address' => env('GEMANA_ADDRESS', null),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    | ISO 4217 code. Defaults to AUD.
    | Will be configurable via Organisation Settings (see backlog).
    |--------------------------------------------------------------------------
    */
    'currency' => env('GEMANA_CURRENCY', 'AUD'),

];

/*
|--------------------------------------------------------------------------
| Add these to your .env file (if not already present)
|--------------------------------------------------------------------------

GEMANA_ADMIN_PREFIX=admin
GEMANA_THEME=gemana-default
GEMANA_ORG_NAME="Your Organisation Name"
GEMANA_ABN="12 345 678 901"
GEMANA_IS_DGR=true
GEMANA_ADDRESS="123 Example Street, City STATE 0000"
GEMANA_CURRENCY=AUD

*/
