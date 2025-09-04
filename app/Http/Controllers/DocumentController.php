<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $this->middleware('auth');
        $user = $request->user();

        $data = $request->validate([
            'type' => 'required|in:id_card,passport,school_certificate,college_certificate,other',
            'name' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120', // 5MB
        ]);

        $path = $request->file('file')->store("uploads/documents/{$user->id}", 'public'); // storage/app/public/...
        $publicPath = 'storage/'.$path; // web-accessible via storage:link

        UserDocument::create([
            'user_id'   => $user->id,
            'type'      => $data['type'],
            'name'      => $data['name'] ?: $request->file('file')->getClientOriginalName(),
            'file_path' => $publicPath,
            'mime'      => $request->file('file')->getClientMimeType(),
            'size'      => $request->file('file')->getSize(),
        ]);

        return back()->with('status', __('Document uploaded.'));
    }

    public function destroy(Request $request, UserDocument $doc)
    {
        $this->middleware('auth');
        abort_unless($doc->user_id === $request->user()->id, 403);

        // remove physical file if present
        $stored = str_starts_with($doc->file_path, 'storage/') ? substr($doc->file_path, 8) : $doc->file_path;
        if (Storage::disk('public')->exists($stored)) {
            Storage::disk('public')->delete($stored);
        }

        $doc->delete();
        return back()->with('status', __('Document removed.'));
    }
}
