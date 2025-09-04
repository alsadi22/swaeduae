<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class BusinessEmailOnly
{
    public function handle(Request $request, Closure $next)
    {
        // Only enforce for POST /org/login
        if (!($request->isMethod('post') && $request->is('org/login'))) {
            return $next($request);
        }

        $email  = strtolower((string) $request->input('email', ''));
        $domain = substr($email, strrpos($email,'@')+1);
        $free   = ['gmail.com','yahoo.com','outlook.com','hotmail.com','live.com','icloud.com','aol.com','mail.com','yandex.com','proton.me'];

        if ($email && in_array($domain, $free, true)) {
            return back()->withErrors(['email' => __('Please use your business email for Organization login.')])->withInput();
        }
        return $next($request);
    }
}
