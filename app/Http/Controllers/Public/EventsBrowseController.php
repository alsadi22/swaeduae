<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;

class EventsBrowseController extends Controller
{
    public function index(Request $request)
    {
        // Reuse the existing public listing logic and view.
        return app(EventController::class)->index($request);
    }
}
