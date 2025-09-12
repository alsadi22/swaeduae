<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class AgentGate {
    public function handle(Request $request, Closure $next){
        if (!config('agent.enabled')) abort(404);
        $provided = $request->query('token') ?: $request->header('X-Agent-Token');
        $expect   = (string) config('agent.token');
        if ($expect === '' || !hash_equals($expect, (string)$provided)) abort(403, 'Agent token required');
        return $next($request);
    }
}
