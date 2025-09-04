<?php
$path = 'app/Http/Middleware/SetLocaleAndHeaders.php';
$code = file_get_contents($path);

// Replace handle() with safe header ops
$code = preg_replace(
    '/public function handle.*\{.*return \$response;\s*\}/s',
<<<'PHPX'
public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    if (method_exists($response, 'headers')) {
        try {
            $locale = app()->getLocale();
            $response->headers->set('Content-Language', $locale);

            $vary = $response->headers->get('Vary');
            if ($vary) {
                if (stripos($vary, 'Accept-Language') === false) {
                    $response->headers->set('Vary', $vary . ',Accept-Language');
                }
            } else {
                $response->headers->set('Vary', 'Accept-Language');
            }

            $cc = $response->headers->get('Cache-Control');
            if (!$response->headers->has('Cache-Control') || stripos((string)$cc, 'public') === false) {
                $response->headers->set('Cache-Control', 'no-cache, private');
            }
        } catch (\Throwable $e) {
            \Log::warning('SetLocaleAndHeaders skipped: '.$e->getMessage());
        }
    }

    return $response;
}
PHPX
, $code, 1);

file_put_contents($path, $code);
echo "Patched $path\n";
