<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        $opportunities = $category->opportunities()->paginate(10);
        return view('categories.show', compact('category','opportunities'));
    }
}
