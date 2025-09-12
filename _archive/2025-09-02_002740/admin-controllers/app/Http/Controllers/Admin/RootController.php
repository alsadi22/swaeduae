<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RootController extends Controller
{
    public function __invoke(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // Authenticated admin → dashboard
            return redirect()->route('admin.dashboard');
        }
        // Guest → admin login
        return redirect()->route('admin.login');
    }
}
