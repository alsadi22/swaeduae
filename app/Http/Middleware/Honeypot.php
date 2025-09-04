<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Honeypot
{
    /** Minimum seconds between form render and submit */
    private int $minDelay = 3;

    public function handle(Request $request, Closure $next): Response
    {
        // Only guard POST requests
        if ($request->isMethod('post')) {
            $trap = trim((string)$request->input('_hp', ''));
            $ts   = (int)$request->input('_hpt', 0);
            $age  = time() - $ts;

            // Bot filled the hidden field OR submitted too fast
            if ($trap !== '' || $age < $this->minDelay) {
                // Optionally log here; we just abort quietly
                abort(422, 'Unprocessable request');
            }
        }
        return $next($request);
    }
}
