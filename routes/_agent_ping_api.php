use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['GET','HEAD'],'agent/ping', function (Request $r) {
    // Accept both X-Agent-Token and Authorization: Bearer
    $hdr = $r->header('X-Agent-Token') ?: preg_replace('/^Bearer\s+/i', '', $r->header('Authorization',''));
    $ok  = hash_equals(config('agent.token',''), (string)$hdr);

    return $ok
        ? response()->json(['ok'=>true,'via'=>'agent.ping.api','env'=>app()->environment()], 200)
        : response()->json(['ok'=>false,'error'=>'forbidden'], 403);
})
->withoutMiddleware([
    'auth', 'auth:api', 'auth:sanctum',
    'throttle', 'throttle:api',
    \App\Http\Middleware\EnforceOrgRegistration::class ?? null,
    \App\Http\Middleware\MicroCache::class ?? null,
    \App\Http\Middleware\SetLocaleAndHeaders::class ?? null,
])
->name('agent.ping.api');
