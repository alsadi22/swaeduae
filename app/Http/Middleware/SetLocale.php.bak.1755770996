<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $fallback  = config('app.locale', 'en');
        $supported = ['en','ar'];
        $lang = session('locale');

        if (!$lang) {
            $pref = $request->getPreferredLanguage($supported);
            $lang = $pref ?: $fallback;
        }

        app()->setLocale($lang);
        return $next($request);
    }
}
