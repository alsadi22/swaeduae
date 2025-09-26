
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('healthz-agent', function (Request $request) {
    $token = config('agent.token');
    if (!$token) {
        return response('Agent token missing', 403);
    }
    $supplied = (string) $request->header('X-Agent-Token', '');
    if (!hash_equals((string) $token, $supplied)) {
        return response('Unauthorized', 401);
    }

    return response()->json([
        'ok'  => true,
        'ts'  => now()->toIso8601String(),
        'app' => config('app.name'),
        'env' => app()->environment(),
        'via' => 'agent.healthz.web',
    ]);
})
// Strip potentially-interfering middlewares (keep this list generous/safe)
->withoutMiddleware([
    'web', // remove web group stack if applied
    'throttle', // any rate limit aliases
    \App\Http\Middleware\EnforceOrgRegistration::class,
    \App\Http\Middleware\MicroCache::class,
    \App\Http\Middleware\SetLocaleAndHeaders::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
])
->name('agent.healthz');
