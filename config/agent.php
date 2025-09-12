<?php
return [
    'enabled'        => env('AGENT_ENABLED', false),
    'token'          => env('AGENT_TOKEN', ''),
    'allow_apply'    => env('AGENT_ALLOW_APPLY', false), // web write actions require this too

    // Smoke checks
    'smoke_urls'     => [
        ['GET','/'],
        ['GET','/contact'],
        ['GET','/contact-us'],
        ['GET','/lang/en'],
        ['GET','/lang/ar'],
    ],

    // Candidate contact views the agent may sanitize
    'contact_candidates' => [
        'resources/views/public/contact.blade.php',
        'resources/views/pages/contact.blade.php',
        'resources/views/contact.blade.php',
    ],

    // Git integration
    'git' => [
        'auto_branch'   => env('AGENT_GIT_AUTOBRANCH', true),
        'branch_prefix' => env('AGENT_GIT_BRANCH_PREFIX', 'agent/fix'),
        'auto_add_push' => env('AGENT_GIT_AUTOPUSH', false), // commit locally; push only if you opt-in
        'author_name'   => env('AGENT_GIT_AUTHOR', 'Agent Bot'),
        'author_email'  => env('AGENT_GIT_EMAIL', 'agent@local'),
        'max_diff_lines'=> 4000,
    ],

    // Crawler (internal sitemap + broken links)
    'crawler' => [
        'enabled'       => env('AGENT_CRAWL_ENABLED', true),
        'max_pages'     => env('AGENT_CRAWL_MAX', 150),
        'timeout'       => env('AGENT_CRAWL_TIMEOUT', 6),
        'user_agent'    => 'AgentCrawler/1.0 (+internal)',
        'respect_sitemap' => true,
        'include_assets'  => false,
    ],
];
