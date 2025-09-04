<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class CanonicalLink {
  public function handle(Request $request, Closure $next){
    $response = $next($request);
    $ct = (string)($response->headers->get('Content-Type',''));
    if ($request->isMethod('GET') && str_contains($ct,'text/html')) {
      $canonical = rtrim($request->url(),'/');
      if (method_exists($response,'header')) { $response->header('Link','<'.$canonical.'>; rel="canonical"'); }
      else { $response->headers->set('Link','<'.$canonical.'>; rel="canonical"', false); }
    }
    return $response;
  }
}
