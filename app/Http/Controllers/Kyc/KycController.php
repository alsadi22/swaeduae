<?php

namespace App\Http\Controllers\Kyc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Volunteer;

class KycController extends Controller
{
    public function showUploadForm()
    {
        return view('kyc.upload');
    }

    public function upload(Request $request)
    {
        $request->validate(['kyc_file' => 'required|file|mimes:pdf,jpg,png|max:2048']);

        $path = $request->file('kyc_file')->store('public/kyc_docs');

        $volunteer = auth()->user()->volunteer;
        $volunteer->kyc_file_path = $path;
        $volunteer->kyc_status = 'pending';
        $volunteer->save();

        return redirect()->route('volunteer.dashboard')->with('success', 'KYC document uploaded.');
    }
}
