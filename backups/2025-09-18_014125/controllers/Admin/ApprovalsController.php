<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\OrgProfile;

class ApprovalsController extends Controller
{
    public function index(Request $request)
    {
        $pending = collect();
        try {
            if (DB::getSchemaBuilder()->hasTable('org_profiles')) {
                $pending = DB::table('org_profiles')
                    ->where('status','pending')
                    ->orderByDesc('created_at')
                    ->limit(500)
                    ->leftJoin('users','users.id','=','org_profiles.user_id')
                    ->select('org_profiles.*','users.email as user_email')
                    ->get();
            }
        } catch (\Throwable $e) {}
        return view('admin.approvals.index', ['pending' => $pending]);
    }

    public function approveOrg($id, Request $request)
    {
        // Load the profile via Eloquent so observers fire and state persists
        $profile = OrgProfile::find($id);
        // Default flash state
        $state = 'nochange';
        if ($profile) {
            // Mark as approved and persist
            $profile->status = 'approved';
            $profile->updated_at = now();
            $profile->save();
            $state = 'approved';
            // Assign the 'org' role on the default guard ('web'); ignore any assignment errors
            if ($profile->user_id) {
                try {
                    $user = User::find($profile->user_id);
                    if ($user && method_exists($user,'assignRole')) {
                        Role::findOrCreate('org', config('auth.defaults.guard','web'));
                        $user->assignRole('org');
                    }
                } catch (\Throwable $e) {
                    // ignore role assignment errors
                }
            }
        }
        return redirect('/admin/approvals')->with('status', $state);
    }

    public function rejectOrg($id, Request $request)
    {
        try {
            $updated = DB::table('org_profiles')->where('id', $id)->update([
                'status'     => 'rejected',
                'updated_at' => now(),
            ]);
            $state = !empty($updated) ? 'rejected' : 'nochange';
            return redirect('/admin/approvals')->with('status', $state);
        } catch (\Throwable $e) {
            return redirect('/admin/approvals')->with('error', 'DB error: '.$e->getMessage());
        }
    }
}
