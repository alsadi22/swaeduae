<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index() { return view('blog.index', ['posts' => BlogPost::latest()->get()]); }
    public function show($id) { return view('blog.show', ['post' => BlogPost::findOrFail($id)]); }
    public function create() { return view('blog.create'); }
    public function store(Request $req)
    {
        $post = BlogPost::create($req->only(['title','body','image']) + ['user_id'=>auth()->id()]);
        return redirect()->route('blog.index')->with('success', 'Post created.');
    }
}
