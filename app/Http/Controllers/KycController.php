<?php
namespace App\Http\Controllers;

use App\Models\Kyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function show()
    {
        $kyc = Kyc::where('user_id', Auth::id())->first();
        return view('kyc_show', compact('kyc'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $kyc = Kyc::updateOrCreate(
            ['user_id' => Auth::id()],
            ['document_path' => $request->file('document')->store('kyc_documents')]
        );

        return redirect()->route('kyc.show')->with('success', 'Document uploaded successfully.');
    }
}
