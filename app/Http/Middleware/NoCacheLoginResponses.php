<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class NoCacheLoginResponses {
  public function handle(Request $r, Closure $next) {
    $res = $next($r);
    if ($r->is('admin/login') || $r->is('org/login') || $r->is('login')) {
      $res->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
      $res->headers->set('Pragma','no-cache');
      $res->headers->set('Expires','Fri, 01 Jan 1990 00:00:00 GMT');
    }
    return $res;
  }
}
