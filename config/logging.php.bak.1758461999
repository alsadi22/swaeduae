<?php

return [

    'default' => env('LOG_CHANNEL', 'daily'),

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            // In CI/tests/healthchecks, avoid external Slack calls if DISABLE_SLACK=1
            'channels' => env('DISABLE_SLACK') ? ['daily']
                : (env('PHPUNIT_RUNNING') ? ['daily']
                : (env('APP_ENV') === 'testing' ? ['daily'] : ['daily', 'slack'])),
            'ignore_exceptions' => false,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'warning'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Swaed Logs',
            'emoji' => ':rotating_light:',
            'level' => 'error', // only ERROR+ goes to Slack
        ],
    ],
];
