<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::paginate(15);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        News::create($request->all());
        return redirect()->route('admin.news.index')->with('success', 'News created.');
    }

    public function edit($id)
    {
        $newsItem = News::findOrFail($id);
        return view('admin.news.edit', compact('newsItem'));
    }

    public function update(Request $request, $id)
    {
        $newsItem = News::findOrFail($id);
        $newsItem->update($request->all());
        return redirect()->route('admin.news.index')->with('success', 'News updated.');
    }

    public function destroy($id)
    {
        $newsItem = News::findOrFail($id);
        $newsItem->delete();
        return redirect()->route('admin.news.index')->with('success', 'News deleted.');
    }
}
