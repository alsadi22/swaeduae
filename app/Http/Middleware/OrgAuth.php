<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class OrgAuth {
  public function handle(Request $request, Closure $next) {
    if (!session()->has('org_id')) {
      return redirect('/org/login');
    }
    return $next($request);
  }
}

