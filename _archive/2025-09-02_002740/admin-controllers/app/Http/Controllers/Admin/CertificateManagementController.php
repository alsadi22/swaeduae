<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateManagementController extends Controller
{
    public function index()
    {
        $certificates = Certificate::all();
        return view('admin.certificates.index', compact('certificates'));
    }
    public function issue($id)
    {
        // Issue certificate logic (to implement)
        return back()->with('success', 'Certificate issued!');
    }
}
