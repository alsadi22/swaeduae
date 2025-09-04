<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminLoginProbe {
    public function handle(Request $request, Closure $next) {
        // Early admin path normalization (before other middlewares)
        if ($request->is('admin/dashboard')) {
            return redirect()->to('https://swaeduae.ae/admin');
        }
        if ($request->is('admin') || $request->is('admin/*')) {
            if (!$request->is('admin/login') && !auth('admin')->check()) {
                return redirect()->to('https://swaeduae.ae/admin/login');
            }
        }

        if (!env('ADMIN_LOGIN_PROBE')) return $next($request);
        if ($request->is('admin/login') && strtoupper($request->method()) === 'POST') {
            $ctx = [
                'path' => $request->path(),
                'ip' => $request->ip(),
                'ua' => substr($request->userAgent() ?? '', 0, 200),
                'has_csrf' => $request->has('_token'),
                'session_id' => $request->session()->getId(),
                'guard' => config('auth.defaults.guard'),
            ];
            Log::debug('ADMIN_LOGIN_PROBE: before', $ctx);
            try {
                $response = $next($request);
                $code = method_exists($response,'getStatusCode') ? $response->getStatusCode() : null;
                Log::debug('ADMIN_LOGIN_PROBE: after', ['status' => $code]);
                return $response;
            } catch (Throwable $e) {
                Log::error('ADMIN_LOGIN_PROBE: exception '.$e->getMessage(), [
                    'class' => get_class($e),
                    'file' => $e->getFile().':'.$e->getLine(),
                ]);
                throw $e;
            }
        }
        return $next($request);
    }
}
