<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logPath)) {
            $logs = array_reverse(explode("\n", file_get_contents($logPath)));
        }

        return view('admin.logs.index', compact('logs'));
    }
}
