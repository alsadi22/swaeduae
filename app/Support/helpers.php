<?php
// Global helper stubs (idempotent)
if (! function_exists('app_env')) {
    function app_env(string $key, $default = null) { return env($key, $default); }
}
if (! function_exists('setting')) {
    function setting(string $key, $default = null) {
        if (class_exists(\App\Models\Setting::class)) {
            return \App\Models\Setting::get($key, $default);
        }
        return $default;
    }
}

