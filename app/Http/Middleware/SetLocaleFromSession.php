<?php
namespace App\Http\Middleware;

use Closure;

class SetLocaleFromSession
{
    public function handle($request, Closure $next)
    {
        $locale = session('locale', config('app.locale', 'en'));
        if (! in_array($locale, ['en','ar'], true)) {
            $locale = config('app.fallback_locale', 'en');
        }
        app()->setLocale($locale);
        return $next($request);
    }
}
