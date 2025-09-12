<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller {
    public function index(){ $items = Media::orderByDesc('id')->paginate(24); return view('admin.media.index', compact('items')); }
    public function upload(Request $r){
        $r->validate(['file'=>'required|image|max:4096']);
        $f=$r->file('file'); $path=$f->store('uploads','public');
        Media::create(['disk'=>'public','path'=>$path,'original_name'=>$f->getClientOriginalName(),'mime'=>$f->getClientMimeType(),'size'=>$f->getSize()]);
        return back()->with('status','Uploaded');
    }
    public function destroy(Media $media){ Storage::disk($media->disk)->delete($media->path); $media->delete(); return back()->with('status','Deleted'); }
}
