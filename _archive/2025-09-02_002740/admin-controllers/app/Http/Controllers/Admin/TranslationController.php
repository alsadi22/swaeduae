<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index()
    {
        // TODO: Load translation strings for editing
        return view('admin.translations.index');
    }

    public function save(Request $request)
    {
        // TODO: Save updated translations
        return redirect()->back()->with('success', 'Translations saved.');
    }
}
