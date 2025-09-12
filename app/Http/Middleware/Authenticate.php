<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            $path = ltrim($request->path(), '/');

            if (str_starts_with($path, 'admin')) {
                return route('admin.login');
            }
            if (str_starts_with($path, 'org')) {
                return route('org.login');
            }
            return route('login');
        }
        return null;
    }
}
