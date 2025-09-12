<?php

return [
    // read DSN from env; allow null if not set
    'dsn' => env('SENTRY_LARAVEL_DSN') ?: env('SENTRY_DSN') ?: null,

    // environment name shown in Sentry
    'environment' => env('SENTRY_ENVIRONMENT', 'production'),

    // optional release tag (e.g., git sha)
    'release' => env('SENTRY_RELEASE'),

    // don't send PII by default
    'send_default_pii' => false,

    // basic breadcrumbs
    'breadcrumbs' => [
        'sql_queries'  => true,
        'sql_bindings' => false,
    ],

    // turn perf/profiling off unless explicitly enabled
    'traces_sample_rate'   => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0),
    'profiles_sample_rate' => (float) env('SENTRY_PROFILES_SAMPLE_RATE', 0),
];
