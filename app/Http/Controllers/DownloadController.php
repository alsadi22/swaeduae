<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Download;

class DownloadController extends Controller
{
    public function index()
    {
        $files = Download::orderBy('created_at', 'desc')->get();
        return view('downloads.index', compact('files'));
    }
}
