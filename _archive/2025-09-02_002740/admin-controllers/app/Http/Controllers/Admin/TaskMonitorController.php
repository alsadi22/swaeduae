<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TaskMonitorController extends Controller
{
    public function index()
    {
        // TODO: Show scheduled tasks and queue status
        return view('admin.task_monitor.index');
    }
}
