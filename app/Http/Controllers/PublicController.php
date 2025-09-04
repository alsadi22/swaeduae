<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        return view("home");
    }
    public function about()     { return view('public.about'); }
    public function services()  { return view('public.services'); }
    public function contact()   { return view('public.contact'); }
}
