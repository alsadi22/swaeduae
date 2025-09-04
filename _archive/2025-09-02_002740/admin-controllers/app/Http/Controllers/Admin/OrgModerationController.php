<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\Organization; use Illuminate\Http\Request;
class OrgModerationController extends Controller {
  public function pending() { $rows = Organization::where('status','pending')->latest()->paginate(20); return view()->first(['admin.orgs.pending','admin.orgs_plain.pending'], compact('rows')); }
  public function show(Organization $org) { return view()->first(['admin.orgs.show','admin.orgs_plain.show'], compact('org')); }
  public function approve(Organization $org, Request $r) { $org->status='approved'; if (property_exists($org,'approved')) $org->approved=true; $org->approved_at=now(); $org->save(); return back()->with('status',__('Approved')); }
  public function reject(Organization $org, Request $r) { $org->status='rejected'; $org->review_notes=$r->input('notes'); $org->save(); return back()->with('status',__('Rejected')); }
}

