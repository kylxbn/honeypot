<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Path
    |--------------------------------------------------------------------------
    | The secret URL path for the honeypot dashboard. Keep this private.
    | Change HONEYPOT_DASHBOARD_PATH in your .env to a secret value.
    */
    'dashboard_path' => env('HONEYPOT_DASHBOARD_PATH', '/dashboard-e3c34cc9c2be9abb5c01'),

    /*
    |--------------------------------------------------------------------------
    | Fake Company Identity
    |--------------------------------------------------------------------------
    | The fake company that this honeypot impersonates.
    */
    'company' => [
        'name'     => 'NovaTech Solutions',
        'tagline'  => 'Empowering Digital Transformation',
        'domain'   => 'novatech-solutions.com',
        'founded'  => '2012',
        'location' => 'San Francisco, CA',
        'email'    => 'info@novatech-solutions.com',
        'phone'    => '+1 (415) 555-0192',
        'wp_version' => '6.4.2',
        'db_name'  => 'novatech_prod',
        'db_user'  => 'novatech_app',
    ],

    /*
    |--------------------------------------------------------------------------
    | MaxMind GeoLite2 Database
    |--------------------------------------------------------------------------
    | Download GeoLite2-Country.mmdb from https://dev.maxmind.com/geoip/geolite2-free-geolocation-data
    | (free account required) and place it at the path below.
    | Country lookup is silently skipped if the file is missing.
    */
    'geoip_db_path' => env('GEOIP_DB_PATH', storage_path('app/GeoLite2-Country.mmdb')),

    /*
    |--------------------------------------------------------------------------
    | Logging Options
    |--------------------------------------------------------------------------
    */
    'log_headers'      => true,
    'log_request_body' => true,

    /*
    |--------------------------------------------------------------------------
    | Paths to skip logging entirely
    |--------------------------------------------------------------------------
    */
    'skip_paths' => [
        '/up',
        '/favicon.ico',
    ],
];
