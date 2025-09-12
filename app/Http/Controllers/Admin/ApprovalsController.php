<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ApprovalsController extends Controller
{
    public function index(Request $request)
    {
        $pending = collect();
        try {
            if (DB::getSchemaBuilder()->hasTable("org_profiles")) {
                $pending = DB::table("org_profiles")
                    ->where("status","pending")
                    ->orderByDesc("created_at")
                    ->limit(500)
                    ->leftJoin("users","users.id","=","org_profiles.user_id")->select("org_profiles.*","users.email as user_email")->get();
            }
        } catch (\Throwable $e) { /* empty */ }
        return view("admin.approvals.index", ["pending"=>$pending]);
    }

    public function approveOrg($id, Request $request)
    {
        try {
            $updated = DB::table("org_profiles")->where("id", $id)->update([
                "status"=>"approved",
                "updated_at"=>now(),
            ]);
        } catch (\Throwable $e) {
            return Redirect::route("admin.approvals.index")->with("error","DB error: ".$e->getMessage());
        }
        return Redirect::route("admin.approvals.index")->with("status", !empty($updated) ? "approved" : "nochange");
    }

    public function rejectOrg($id, Request $request)
    {
        try {
            $updated = DB::table("org_profiles")->where("id", $id)->update([
                "status"=>"rejected",
                "updated_at"=>now(),
            ]);
        } catch (\Throwable $e) {
            return Redirect::route("admin.approvals.index")->with("error","DB error: ".$e->getMessage());
        }
        return Redirect::route("admin.approvals.index")->with("status", !empty($updated) ? "rejected" : "nochange");
    }
}
