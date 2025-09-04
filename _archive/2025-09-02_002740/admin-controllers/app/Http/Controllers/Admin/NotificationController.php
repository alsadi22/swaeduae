<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function pollPendingKyc()
    {
        // Example logic: fetch count of pending KYCs
        $count = \App\Models\Kyc::where('status', 'pending')->count();
        return response()->json(['pending_kyc' => $count]);
    }
}
