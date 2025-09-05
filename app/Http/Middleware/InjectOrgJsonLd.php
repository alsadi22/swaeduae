<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectOrgJsonLd
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $ct = $response->headers->get('Content-Type');
        if (!$ct || stripos((string)$ct, 'text/html') === false) return $response;

        $html = $response->getContent();
        if (!is_string($html)) return $response;

        if (stripos($html, 'application/ld+json') !== false) return $response;

        $json = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => config('app.name', 'SwaedUAE'),
            'url'      => url('/'),
            'logo'     => url('/favicon.ico'),
        ];
        $script = '<script type="application/ld+json">'.json_encode($json, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>';

        if (stripos($html, '</head>') !== false) {
            $html = preg_replace('~</head>~i', "    {$script}\n</head>", $html, 1);
        } elseif (stripos($html, '</body>') !== false) {
            $html = preg_replace('~</body>~i', "    {$script}\n</body>", $html, 1);
        } else {
            $html = $script . "\n" . $html;
        }

        $response->setContent($html);
        return $response;
    }
}
