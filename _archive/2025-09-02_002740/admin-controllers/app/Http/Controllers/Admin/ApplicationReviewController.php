<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApplicationReviewController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->check(), 403);

        $user = $request->user();
        if (!($user->can('admin') || ($user->is_admin ?? false) || $user->hasRole('admin'))) {
            abort(403);
        }

        if (!Schema::hasTable('applications')) {
            return view('admin.applications.index', [
                'apps' => collect(),
                'message' => 'Applications table not found.',
                'filters' => [],
            ]);
        }

        $filters = [
            'status' => $request->get('status'),
            'q'      => $request->get('q'),
        ];

        $query = DB::table('applications as a')
            ->select('a.*', 'u.name as user_name', 'u.email', 'o.title as opportunity_title', 'o.capacity')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('opportunities as o', 'o.id', '=', 'a.opportunity_id')
            ->orderBy('a.created_at', 'desc');

        if ($filters['status']) $query->where('a.status', $filters['status']);
        if ($filters['q']) {
            $q = '%'.$filters['q'].'%';
            $query->where(function($w) use ($q) {
                $w->where('u.name','like',$q)->orWhere('u.email','like',$q)->orWhere('o.title','like',$q);
            });
        }

        $apps = $query->paginate(20)->appends($request->query());

        return view('admin.applications.index', compact('apps','filters'));
    }

    public function bulk(Request $request)
    {
        abort_unless(auth()->check(), 403);
        $user = $request->user();
        if (!($user->can('admin') || ($user->is_admin ?? false) || $user->hasRole('admin'))) {
            abort(403);
        }

        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (!is_array($ids) || empty($ids)) {
            return back()->with('error', __('No items selected.'));
        }

        if (!in_array($action, ['approve','waitlist','reject','pending','cancelled'])) {
            return back()->with('error', __('Invalid action.'));
        }

        $apps = DB::table('applications')->whereIn('id', $ids)->get();
        foreach ($apps as $app) {
            $status = $action;

            if ($action === 'approve') {
                $capacity = DB::table('opportunities')->where('id', $app->opportunity_id)->value('capacity');
                if (!empty($capacity)) {
                    $approvedCount = DB::table('applications')
                        ->where('opportunity_id', $app->opportunity_id)
                        ->where('status', 'approved')
                        ->count();

                    if ($approvedCount >= $capacity) {
                        $status = 'waitlisted';
                    }
                }
            }

            DB::table('applications')->where('id', $app->id)->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', __('Updated :n applications.', ['n' => count($ids)]));
    }
}
