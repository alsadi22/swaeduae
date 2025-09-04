<?php
$path = 'app/Http/Middleware/MicroCache.php';
$code = <<<'PHP2'
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class MicroCache
{
    public function handle(Request $request, Closure $next, $ttl = '120'): Response
    {
        // Only GET, allow bypass flag
        if (!$request->isMethod('GET') || $request->query('_nocache') !== null) {
            return $next($request);
        }

        // Skip interactive/sensitive areas and authenticated users
        if ($request->user()
            || $request->is('admin/*')
            || $request->is('login')
            || $request->is('register')
            || $request->is('password/*')
            || $request->is('dashboard*')
            // NEW: skip public forms that render CSRF tokens
            || $request->is('org/register')
            || $request->is('contact')
            || $request->is('volunteer/*/register')) {
            return $next($request);
        }

        $ttl = (int) $ttl;
        $key = 'microcache:'.$ttl.':'.$request->getHost().':'.$request->getRequestUri().':'.app()->getLocale();

        if ($cached = Cache::get($key)) {
            $etag = $cached['etag'];
            $ifNone = $request->headers->get('If-None-Match');
            if ($ifNone && trim($ifNone, '"') === $etag) {
                return response('', 304)
                    ->header('ETag', '"'.$etag.'"')
                    ->header('Cache-Control', "public, max-age=$ttl, s-maxage=$ttl")
                    ->header('X-MicroCache', 'HIT-304');
            }

            return response($cached['body'], 200, $cached['headers'])
                ->header('ETag', '"'.$etag.'"')
                ->header('Cache-Control', "public, max-age=$ttl, s-maxage=$ttl")
                ->header('X-MicroCache', 'HIT');
        }

        /** @var \Symfony\Component\HttpFoundation\Response $resp */
        $resp = $next($request);

        // Only cache successful HTML
        if (method_exists($resp, 'getStatusCode')
            && $resp->getStatusCode() === 200
            && str_contains(strtolower($resp->headers->get('Content-Type', '')), 'text/html')) {

            $body = $resp->getContent();

            // NEW: do NOT cache pages that embed CSRF tokens or forms
            if (preg_match('/name=["\']_token["\']|meta\s+name=["\']csrf-token["\']/i', $body)) {
                $resp->headers->set('X-MicroCache', 'SKIP-CSRF');
                return $resp;
            }

            $etag = sha1($body);
            Cache::put($key, [
                'body' => $body,
                'headers' => [
                    'Content-Type' => $resp->headers->get('Content-Type', 'text/html; charset=UTF-8'),
                ],
                'etag' => $etag,
            ], $ttl);

            $resp->headers->set('ETag', '"'.$etag.'"');
            $resp->headers->set('Cache-Control', "public, max-age=$ttl, s-maxage=$ttl");
            $resp->headers->set('X-MicroCache', 'MISS');
        }

        return $resp;
    }
}
PHP2;

copy($path, $path.'.bak_'.date('Ymd_His'));
file_put_contents($path, $code);
echo "[OK] MicroCache updated to skip CSRF/form pages\n";
