<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;

class KycManagementController extends Controller
{
    public function index()
    {
        $kycs = Kyc::all();
        return view('admin.kyc.index', compact('kycs'));
    }
}
