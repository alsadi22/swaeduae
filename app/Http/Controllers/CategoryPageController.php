<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryPageController extends Controller
{
    public function index()
    {
        $categories = Opportunity::query()
            ->selectRaw("category, COUNT(*) as cnt")
            ->whereNotNull('category')->where('category','<>','')
            ->groupBy('category')->orderBy('category')->get();
        return view('categories/index', compact('categories'));
    }

    public function show($slug)
    {
        $name = str_replace('-', ' ', Str::lower($slug));
        $list = Opportunity::query()
            ->whereRaw('LOWER(category) = ?', [$name])
            ->latest('starts_at')->paginate(12)->withQueryString();
        return view('categories/show', ['category'=>$name, 'list'=>$list]);
    }
}
