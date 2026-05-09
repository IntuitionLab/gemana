<?php

/*
|--------------------------------------------------------------------------
| Gemana Module Registry
|--------------------------------------------------------------------------
| This file is the master list of all known modules.
| The ModuleLoader uses module.json inside each module folder as the
| source of truth — this file serves as a human-readable reference
| and can be used to pre-seed the Super-Admin modules panel.
|
| Each module's enabled/disabled state is controlled via its module.json.
*/

return [

    'Members' => [
        'description' => 'Member registration, profiles, and tiered membership levels.',
        'core' => true,   // Core modules cannot be disabled
    ],

    'Donations' => [
        'description' => 'Donation processing, DGR tax receipts, and contribution history.',
        'core' => false,
    ],

    'Events' => [
        'description' => 'Event creation, ticketing, and attendee management.',
        'core' => false,
    ],

    'Blog' => [
        'description' => 'News and blog posts for the public-facing website.',
        'core' => false,
    ],

    'Documents' => [
        'description' => 'Document library with role-based access control.',
        'core' => false,
    ],

    'Notifications' => [
        'description' => 'Email and in-app notification system.',
        'core' => true,
    ],

    'Newsletter' => [
        'description' => 'Email newsletter campaigns for members and subscribers.',
        'core' => false,
    ],

    'Volunteering' => [
        'description' => 'Volunteer rostering, availability, and check-ins.',
        'core' => false,
    ],

];
