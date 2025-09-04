<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrScan;

class QrScanController extends Controller
{
    public function index()
    {
        $scans = QrScan::orderBy('scanned_at', 'desc')->paginate(50);
        return view('admin.qr_scans.index', compact('scans'));
    }
}
