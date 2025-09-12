<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class VolunteerHoursAdminController extends Controller
{
    // POST /admin/hours/{id}/approve
    public function approve($id)
    {
        $row = DB::table('volunteer_hours')->where('id', $id)->first();
        if (!$row) {
            return back()->with('error', __('Record not found.'));
        }

        $new = ($row->approved ? 0 : 1);
        DB::table('volunteer_hours')->where('id', $id)->update([
            'approved'   => $new,
            'updated_at' => now(),
        ]);

        return back()->with('success', $new ? __('Approved') : __('Unapproved'));
    }
}
