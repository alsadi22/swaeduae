<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApprovalsController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $pending = DB::table('org_profiles')
                ->leftJoin('users', 'users.id', '=', 'org_profiles.user_id')
                ->select('org_profiles.*', 'users.email as user_email')
                ->where('org_profiles.status', 'pending')
                ->orderByDesc('org_profiles.created_at')
                ->limit(500)
                ->get();
        } catch (\Throwable $e) {
            $pending = collect();
        }

        return view('admin.approvals.index', ['pending' => $pending]);
    }

    public function approveOrg(int $id, Request $request): RedirectResponse
    {
        try {
            DB::table('org_profiles')->where('id', $id)->update([
                'status' => 'approved',
                'approved_at' => now(),
                'updated_at' => now(),
            ]);
            $request->session()->flash('status', 'approved');
        } catch (\Throwable $e) {
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->route('admin.approvals.index');
    }

    public function rejectOrg(int $id, Request $request): RedirectResponse
    {
        try {
            DB::table('org_profiles')->where('id', $id)->update([
                'status' => 'rejected',
                'updated_at' => now(),
            ]);
            $request->session()->flash('status', 'rejected');
        } catch (\Throwable $e) {
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->route('admin.approvals.index');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        try {
            $pending = DB::table('org_profiles')
                ->leftJoin('users', 'users.id', '=', 'org_profiles.user_id')
                ->select('org_profiles.id', 'org_profiles.org_name', 'users.email as user_email', 'org_profiles.org_code', 'org_profiles.emirate', 'org_profiles.created_at')
                ->where('org_profiles.status', 'pending')
                ->orderByDesc('org_profiles.created_at')
                ->limit(500)
                ->get();
        } catch (\Throwable $e) {
            $pending = collect();
        }

        $callback = static function () use ($pending): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'org_name', 'user_email', 'org_code', 'emirate', 'created_at']);
            foreach ($pending as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->org_name,
                    $row->user_email,
                    $row->org_code,
                    $row->emirate,
                    $row->created_at,
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, 'approvals.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
