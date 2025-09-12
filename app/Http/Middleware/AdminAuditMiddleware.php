<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminAuditMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $user = $request->user();
            $isAdmin = false;
            if ($user) {
                $isAdmin = ($user->is_admin ?? false)
                    || (method_exists($user,'can') && $user->can('admin'))
                    || (method_exists($user,'hasRole') && $user->hasRole('admin'));
            }

            $path = ltrim($request->path(), '/');
            $looksAdminPath = Str::startsWith($path, 'admin');

            if (!($isAdmin || $looksAdminPath)) {
                return $response;
            }

            if (!Schema::hasTable('audit_logs')) {
                return $response;
            }

            $payload = $request->except(['password','password_confirmation','_token']);
            $excerpt = substr(json_encode($payload, JSON_UNESCAPED_UNICODE), 0, 2000);
            $routeName = optional($request->route())->getName();

            $data = [
                'user_id'        => $user->id ?? null,
                'method'         => $request->method(),
                'route'          => '/'.$path,
                'route_name'     => $routeName,
                'ip'             => $request->ip(),
                'user_agent'     => substr((string)$request->header('User-Agent'),0,255),
                'payload_excerpt'=> $excerpt,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            if (Schema::hasColumn('audit_logs','role'))    $data['role'] = $isAdmin ? 'admin' : null;
            if (Schema::hasColumn('audit_logs','action'))  $data['action'] = $routeName ?: $data['method'];
            if (Schema::hasColumn('audit_logs','path'))    $data['path'] = '/'.$path;
            if (Schema::hasColumn('audit_logs','payload')) $data['payload'] = json_encode([
                'excerpt' => $excerpt,
                'method'  => $request->method(),
                'route'   => $routeName,
            ], JSON_UNESCAPED_UNICODE);
            if (Schema::hasColumn('audit_logs','meta'))    $data['meta'] = json_encode(['ua'=>$data['user_agent']], JSON_UNESCAPED_UNICODE);

            DB::table('audit_logs')->insert($data);
        } catch (\Throwable $e) {
            // silent
        }

        return $response;
    }
}
