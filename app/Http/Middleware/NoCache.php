<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class NoCache {
  public function handle(Request $request, Closure $next) {
    $resp = $next($request);
    $resp->headers->set('Cache-Control','no-store, no-cache, must-revalidate, max-age=0');
    $resp->headers->set('Pragma','no-cache');
    $resp->headers->set('Expires','Fri, 01 Jan 1990 00:00:00 GMT');
    return $resp;
  }
}
