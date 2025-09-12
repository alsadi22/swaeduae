<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCertificateController extends Controller
{
    public function index()
    {
        $certs = DB::table('certificates as c')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('events as e', 'e.id', '=', 'c.event_id')
            ->orderByDesc('c.issued_at')
            ->select([
                'c.code',
                'c.status',
                'c.issued_at',
                'u.name as user_name',
                'u.email as user_email',
                'e.title as event_title',
            ])
            ->paginate(20);

        return view('admin.certificates.index', compact('certs'));
    }

    public function create()
    {
        return view('admin.certificates.issue');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_email' => 'required|email',
            'event_id' => 'nullable|integer|exists:events,id',
            'code' => 'nullable|string|max:191',
        ]);

        $user = DB::table('users')->where('email', $data['user_email'])->first();
        if (!$user) {
            return back()->withErrors(['user_email' => __('swaed.user_not_found')])->withInput();
        }

        $code = $data['code'] ?? $this->generateCode();

        // Ensure unique code
        $exists = DB::table('certificates')->where('code', $code)->exists();
        if ($exists) {
            return back()->withErrors(['code' => __('swaed.code_exists')])->withInput();
        }

        DB::table('certificates')->insert([
            'user_id' => $user->id,
            'event_id' => $data['event_id'] ?? null,
            'code' => $code,
            'issued_at' => now(),
            'status' => 'issued',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.certificates.index')
            ->with('status', __('swaed.certificate_issued') . ' ' . $code);
    }

    private function generateCode(int $len = 10): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';
        for ($i=0; $i<$len; $i++) $out .= $chars[random_int(0, strlen($chars)-1)];
        return $out;
    }
}
