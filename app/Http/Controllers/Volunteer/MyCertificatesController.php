<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyCertificatesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $certs = \App\Models\Certificate::with(['opportunity:id,title'])
            ->where('user_id', $user->id)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('volunteer.certificates.index', [
            'certs' => $certs,
        ]);
    }
}
