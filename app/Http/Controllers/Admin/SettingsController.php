<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    protected function file(){ return 'settings.json'; }

    public function index(){
        $data = [];
        if (Storage::disk('local')->exists($this->file())) {
            $data = json_decode(Storage::disk('local')->get($this->file()), true) ?: [];
        }
        return view('admin.settings.index', ['s'=>$data]);
    }

    public function save(Request $r){
        $data = $r->validate([
            'site_name'=>'nullable|string|max:120',
            'locale_default'=>'nullable|in:en,ar',
            'mail_from_name'=>'nullable|string|max:120',
            'mail_from_address'=>'nullable|email',
            'plausible_domain'=>'nullable|string|max:190',
            'sentry_dsn'=>'nullable|string|max:300',
        ]);
        Storage::disk('local')->put($this->file(), json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
        return back()->with('ok','Settings saved');
    }
}
