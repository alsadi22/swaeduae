<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::orderByDesc('published_at')->paginate(10);
        return view('stories.index', compact('stories'));
    }

    public function show(Story $story)
    {
        return view('stories.show', compact('story'));
    }
}
