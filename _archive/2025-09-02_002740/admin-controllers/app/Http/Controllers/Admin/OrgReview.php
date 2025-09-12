<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrgReview extends Controller
{
    public function approve(Request $request, $organization)
    {
        // TODO: implement real approval; placeholder keeps routes healthy
        return abort(404);
    }

    public function reject(Request $request, $organization)
    {
        // TODO: implement real rejection; placeholder keeps routes healthy
        return abort(404);
    }

    // For any accidental single-action usage
    public function __invoke(Request $request)
    {
        return abort(404);
    }
}
