<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Certificate;

class CertificateController extends Controller
{
    public function index()
    {
        $volunteer = Auth::user();
        $certificates = $volunteer->certificates;
        return view('volunteer.certificates', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        return view('certificates.show', compact('certificate'));
    }
}
