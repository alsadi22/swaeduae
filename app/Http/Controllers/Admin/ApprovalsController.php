<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrgProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ApprovalsController extends Controller
{
    public function index(): View
    {
        $orgs = OrgProfile::where('status', 'pending')
            ->with('user:id,email')
            ->orderBy('created_at')
            ->get();

        return view('admin.approvals.index', compact('orgs'));
    }

    public function approveOrg(int $id): RedirectResponse
    {
        return $this->updateOrg($id, 'approved');
    }

    public function declineOrg(int $id): RedirectResponse
    {
        return $this->updateOrg($id, 'rejected');
    }

    protected function updateOrg(int $id, string $status): RedirectResponse
    {
        DB::transaction(function () use ($id, $status) {
            $profile = OrgProfile::lockForUpdate()->findOrFail($id);
            $profile->update(['status' => $status]);

            $user = $profile->user;
            if ($user) {
                if ($status === 'approved') {
                    if (Schema::hasColumn('users', 'role')) {
                        $user->role = 'org';
                        $user->save();
                    }
                    if (method_exists($user, 'assignRole')) {
                        try { $user->assignRole('org'); } catch (\Throwable $e) {}
                    }
                } else {
                    if (Schema::hasColumn('users', 'role') && ($user->role ?? null) === 'org') {
                        $user->role = null;
                        $user->save();
                    }
                    if (method_exists($user, 'removeRole')) {
                        try { $user->removeRole('org'); } catch (\Throwable $e) {}
                    }
                }
            }
        });

        $msg = $status === 'approved' ? 'Organization approved.' : 'Organization declined.';

        return redirect()->back()->with('status', $msg);
    }
}
