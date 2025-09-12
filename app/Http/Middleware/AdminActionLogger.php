<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AdminActionLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // log only mutating methods under /admin
        if (!str_starts_with($request->path(), 'admin')) return $response;
        if (!in_array($request->method(), ['POST','PUT','PATCH','DELETE'])) return $response;

        $user = $request->user();
        $route = $request->route();

        $input = $request->except([
            'password','password_confirmation','current_password',
            'token','_token','_method','file','files','logo','photo','keyfile',
            'google_client_secret','apple_key','payment_secret','payment_private','secret','api_key'
        ]);

        $meta = [
            'status' => $response->getStatusCode(),
            'ip'     => $request->ip(),
            'ua'     => substr((string)$request->userAgent(),0,255),
        ];

        // optional model binding detection
        $modelType = null; $modelId = null;
        foreach ((array)$route?->parameters ?? [] as $param) {
            if (is_object($param) && method_exists($param,'getKey') && method_exists($param,'getMorphClass')) {
                $modelType = $param->getMorphClass();
                $modelId   = (string)$param->getKey();
                break;
            }
        }

        AuditLog::record([
            'user_id'    => $user?->id,
            'action'     => $route?->getName() ?? 'admin.request',
            'method'     => $request->method(),
            'route_name' => $route?->getName(),
            'path'       => '/'.$request->path(),
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'payload'    => $input,
            'meta'       => $meta,
        ]);

        return $response;
    }
}
