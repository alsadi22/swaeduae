<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;

class HealthzController extends Controller
{
    public function __invoke(): Response
    {
        return response('OK', 200)->header('Cache-Control', 'no-store');
    }
}
