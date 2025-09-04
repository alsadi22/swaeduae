<?php
namespace App\Http\Controllers\PublicSite;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Controller;

class ApplyController extends Controller
{
    public function form(Request $req)
    {
        $targetType = $req->query('type','opportunity'); // 'event'|'opportunity'
        $targetId   = (int) $req->query('id', 0);
        abort_unless($targetId > 0, 404);

        return view('public/apply', compact('targetType','targetId'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'target_type' => 'required|in:event,opportunity',
            'target_id'   => 'required|integer|min:1',
            'name'        => 'required|string|max:200',
            'email'       => 'required|email:rfc,dns',
            'phone'       => 'nullable|string|max:100',
            'message'     => 'nullable|string|max:2000',
        ]);

        $now = now();
        $saved = false;

        // Prefer canonical applications table if present
        if (Schema::hasTable('applications')) {
            $payload = [
                'user_id'      => auth()->id(),
                'opportunity_id' => $data['target_type']==='opportunity' ? $data['target_id'] : null,
                'event_id'       => $data['target_type']==='event'       ? $data['target_id'] : null,
                'status'       => DB::getSchemaBuilder()->hasColumn('applications','status') ? 'pending' : null,
                'created_at'   => $now, 'updated_at' => $now,
            ];
            // keep submitter info in a meta column if it exists; else ignore
            if (DB::getSchemaBuilder()->hasColumn('applications','meta')) {
                $payload['meta'] = json_encode([
                    'name'=>$data['name'],'email'=>$data['email'],
                    'phone'=>$data['phone'],'message'=>$data['message']
                ], JSON_UNESCAPED_UNICODE);
            }
            $saved = DB::table('applications')->insert($payload);
        }

        // Fallback to event_info_requests (exists in your schema) if needed
        if (!$saved && Schema::hasTable('event_info_requests')) {
            $payload = [
                'event_id'   => $data['target_type']==='event' ? $data['target_id'] : null,
                'name'       => $data['name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'],
                'message'    => $data['message'],
                'created_at' => $now, 'updated_at' => $now,
            ];
            $saved = DB::table('event_info_requests')->insert($payload);
        }

        // Last‑resort generic inbox if neither table exists
        if (!$saved && Schema::hasTable('contact_messages')) {
            $payload = [
                'subject'    => strtoupper($data['target_type'])." #".$data['target_id']." Apply",
                'name'       => $data['name'],
                'email'      => $data['email'],
                'message'    => $data['message'] ?: '—',
                'created_at' => $now, 'updated_at' => $now,
            ];
            $saved = DB::table('contact_messages')->insert($payload);
        }

        return redirect()->back()->with('status', $saved ? __('Application received.') : __('Saved to inbox.'));
    }
}
