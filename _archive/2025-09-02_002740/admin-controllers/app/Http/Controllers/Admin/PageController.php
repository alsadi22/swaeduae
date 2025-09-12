<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    // List all editable pages
    public function index()
    {
        $pages = ['about', 'contact', 'faq', 'partners', 'stories'];
        $contents = [];
        foreach($pages as $page) {
            $path = "public/pages/{$page}.html";
            $contents[$page] = Storage::exists($path) ? Storage::get($path) : '';
        }
        return view('admin.pages', compact('pages','contents'));
    }

    // Save edits to a page
    public function update(Request $request, $page)
    {
        $this->validate($request, [
            'content' => 'required|string',
        ]);
        Storage::put("public/pages/{$page}.html", $request->content);
        return back()->with('success', ucfirst($page).' page updated.');
    }
}
