<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;

class KycAdminController extends Controller
{
    public function index()
    {
        $kycs = Kyc::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.kyc.index', compact('kycs'));
    }

    public function show($id)
    {
        $kyc = Kyc::findOrFail($id);
        return view('admin.kyc.show', compact('kyc'));
    }

    public function approve($id)
    {
        $kyc = Kyc::findOrFail($id);
        $kyc->status = 'approved';
        $kyc->save();

        return redirect()->route('admin.kyc.index')->with('success', 'KYC approved.');
    }

    public function reject($id)
    {
        $kyc = Kyc::findOrFail($id);
        $kyc->status = 'rejected';
        $kyc->save();

        return redirect()->route('admin.kyc.index')->with('success', 'KYC rejected.');
    }
}
