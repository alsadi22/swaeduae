<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use App\Models\Organization;
class OrgApproved {
  public function handle(Request $request, Closure $next) {
    $id = session('org_id');
    $org = $id ? Organization::find($id) : null;
    if (!$org) { return redirect('/org/login'); }
    $status = $org->status ?? ($org->approved ? 'approved' : 'pending');
    if ($status !== 'approved') { return redirect('/org/pending'); }
    // attach to request for controllers if needed
    $request->attributes->set('org', $org);
    return $next($request);
  }
}

