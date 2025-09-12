<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromRequest
{
    protected array $allowed = ['ar','en'];
    protected string $fallback = 'ar';

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('lang')
            ?? $request->cookie('app_locale')
            ?? $this->fallback;

        if (! in_array($locale, $this->allowed, true)) {
            $locale = $this->fallback;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
