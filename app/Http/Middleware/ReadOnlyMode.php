<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ReadOnlyMode
{
    /** @var string[] */
    protected array $allow = [
        // e.g. 'admin/login', 'health', 'status'
    ];

    public function handle(Request $request, Closure $next)
    {
        if (config('app.readonly', env('APP_READONLY', false))) {
            if (in_array(strtoupper($request->method()), ['POST','PUT','PATCH','DELETE'], true)) {
                foreach ($this->allow as $prefix) {
                    if ($request->is($prefix.'*')) {
                        return $next($request);
                    }
                }
                return response()->view('errors.readonly', [], 503);
            }
        }
        return $next($request);
    }
}
