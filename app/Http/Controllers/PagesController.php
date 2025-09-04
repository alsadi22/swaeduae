<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function regional($region)
    {
        return view("pages.region", compact('region'));
    }

    public function faq()
    {
        return view("pages.faq");
    }

    public function about()
    {
        return view("pages.about");
    }

    public function contact()
    {
        return view("pages.contact");
    }

    public function partners()
    {
        return view("pages.partners");
    }

    public function license()
    {
        return response()->download(public_path('pdfs/volunteer_guidelines.pdf'));
    }

    public function app()
    {
        return view("pages.app");
    }
}
