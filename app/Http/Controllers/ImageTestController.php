<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;

class ImageTestController extends Controller
{
    public function index()
    {
        $imagesDir = public_path('images');
        $partnersDir = public_path('partners');

        $images = File::files($imagesDir);
        $partners = File::files($partnersDir);

        return view('image-test', [
            'images' => $images,
            'partners' => $partners
        ]);
    }
}
