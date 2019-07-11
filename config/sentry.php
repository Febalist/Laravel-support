<?php

return [
    'dsn' => env('SENTRY_DSN'),

    // capture release as git sha
    'release' => @file_get_contents(base_path('VERSION')) ?: '0.0.0',

    'breadcrumbs' => [
        // Capture bindings on SQL queries logged in breadcrumbs
        'sql_bindings' => true,
    ],
];
