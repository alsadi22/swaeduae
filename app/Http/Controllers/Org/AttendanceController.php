<?php
namespace App\Http\Controllers\Org;
use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    const SCAN_COOLDOWN_SECONDS = 10;
    const RATE_LIMIT_MAX = 6;   // scans
    const RATE_LIMIT_WINDOW = 5; // seconds

    public function scan(Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);
        return view('org.attendance.scan', ['opportunity'=>$opportunity,'csrf'=>csrf_token()]);
    }

    public function checkin(Request $request, Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);
        if ($msg = $this->throttleMessage($opportunity->id)) {
            return response()->json(['ok'=>false,'message'=>$msg], 429);
        }

        $payload = trim((string)($request->input('qr') ?? ''));
        if ($payload==='') return response()->json(['ok'=>false,'message'=>'QR payload missing'],422);

        $lat = $request->input('lat'); $lng = $request->input('lng'); $acc = $request->input('acc');

        $parsed = $this->parsePayload($payload);
        $userId = $parsed['user_id'] ?? $this->resolveVolunteerFromPayload($payload);
        if (!$userId) { $this->logScan($opportunity->id,'checkin',$payload,false,'volunteer_not_found',null,$lat,$lng,$acc); return response()->json(['ok'=>false,'message'=>'Volunteer not found'],404); }

        if ($this->recentDuplicate($opportunity->id,$userId,'checkin')) return response()->json(['ok'=>true,'message'=>'Duplicate scan (ignored)']);

        if (!$this->passesGeofence($opportunity->id, $lat, $lng)) {
            $this->logScan($opportunity->id,'checkin',$payload,false,'outside_geofence',$userId,$lat,$lng,$acc);
            return response()->json(['ok'=>false,'message'=>'Outside geofence area'], 403);
        }

        // Already open?
        $open = DB::table('attendances')->where('opportunity_id',$opportunity->id)->where('user_id',$userId)->whereNull('check_out_at')->orderByDesc('id')->first();
        if ($open) { $this->logScan($opportunity->id,'checkin',$payload,true,'already_open',$userId,$lat,$lng,$acc); return response()->json(['ok'=>true,'message'=>'Already checked-in']); }

        $now = now();
        $insert = [
            'opportunity_id'=>$opportunity->id, 'user_id'=>$userId, 'status'=>'present',
            'check_in_at'=>$now, 'minutes'=>0, 'created_at'=>$now, 'updated_at'=>$now,
        ];
        if (Schema::hasColumn('attendances','check_in_lat')) {
            $insert += ['check_in_lat'=>$lat,'check_in_lng'=>$lng,'check_in_acc'=>$acc];
        }
        DB::table('attendances')->insert($insert);

        $this->logScan($opportunity->id,'checkin',$payload,true,null,$userId,$lat,$lng,$acc);
        return response()->json(['ok'=>true,'message'=>'Check-in recorded']);
    }

    public function checkout(Request $request, Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);
        if ($msg = $this->throttleMessage($opportunity->id)) {
            return response()->json(['ok'=>false,'message'=>$msg], 429);
        }

        $payload = trim((string)($request->input('qr') ?? ''));
        if ($payload==='') return response()->json(['ok'=>false,'message'=>'QR payload missing'],422);

        $lat = $request->input('lat'); $lng = $request->input('lng'); $acc = $request->input('acc');

        $parsed = $this->parsePayload($payload);
        $userId = $parsed['user_id'] ?? $this->resolveVolunteerFromPayload($payload);
        if (!$userId) { $this->logScan($opportunity->id,'checkout',$payload,false,'volunteer_not_found',null,$lat,$lng,$acc); return response()->json(['ok'=>false,'message'=>'Volunteer not found'],404); }

        if ($this->recentDuplicate($opportunity->id,$userId,'checkout')) return response()->json(['ok'=>true,'message'=>'Duplicate scan (ignored)']);

        if (!$this->passesGeofence($opportunity->id, $lat, $lng)) {
            $this->logScan($opportunity->id,'checkout',$payload,false,'outside_geofence',$userId,$lat,$lng,$acc);
            return response()->json(['ok'=>false,'message'=>'Outside geofence area'], 403);
        }

        $open = DB::table('attendances')->where('opportunity_id',$opportunity->id)->where('user_id',$userId)->whereNull('check_out_at')->orderByDesc('id')->first();
        if (!$open) { $this->logScan($opportunity->id,'checkout',$payload,false,'no_open_session',$userId,$lat,$lng,$acc); return response()->json(['ok'=>false,'message'=>'No open check-in found'],409); }

        $now = now(); $checkIn = \Illuminate\Support\Carbon::parse($open->check_in_at);
        $mins = max(1,(int)ceil($checkIn->diffInSeconds($now)/60));
        $update = ['check_out_at'=>$now,'minutes'=>$mins,'updated_at'=>$now];
        if (Schema::hasColumn('attendances','check_out_lat')) $update += ['check_out_lat'=>$lat,'check_out_lng'=>$lng,'check_out_acc'=>$acc];
        DB::table('attendances')->where('id',$open->id)->update($update);

        $this->logScan($opportunity->id,'checkout',$payload,true,null,$userId,$lat,$lng,$acc);
        return response()->json(['ok'=>true,'message'=>'Check-out recorded','minutes_added'=>$mins]);
    }

    public function undo(Request $request, int $attendanceId)
    {
        $row = DB::table('attendances')->where('id',$attendanceId)->first();
        if (!$row) return back()->with('status','Attendance not found.');
        $this->authorize('manage-attendance', $attendanceId);

        if ($row->check_out_at) {
            DB::table('attendances')->where('id',$attendanceId)->update(['check_out_at'=>null,'minutes'=>0,'updated_at'=>now()]);
            $msg='Checkout undone; session reopened.';
        } else {
            if (Schema::hasColumn('attendances','deleted_at')) DB::table('attendances')->where('id',$attendanceId)->update(['deleted_at'=>now(),'updated_at'=>now()]);
            else DB::table('attendances')->where('id',$attendanceId)->delete();
            $msg='Open check-in removed.';
        }
        DB::table('audit_logs')->insert([
            'actor_id'=>Auth::id(),'action'=>'attendance.undo','entity_type'=>'attendance','entity_id'=>$attendanceId,
            'note'=>json_encode(['reason'=>$request->input('reason'),'ip'=>request()->ip()]),'created_at'=>now(),'updated_at'=>now(),
        ]);
        return back()->with('status',$msg);
    }

    /* ---------- Helpers ---------- */

    private function throttleMessage(int $oppId): ?string
    {
        $ip = request()->ip();
        $key = "scan:opp:$oppId:ip:$ip";
        $now = now()->timestamp;
        $bucket = Cache::get($key, []);
        $bucket = array_values(array_filter($bucket, fn($ts)=>($now - $ts) < self::RATE_LIMIT_WINDOW));
        $bucket[] = $now;
        Cache::put($key, $bucket, self::RATE_LIMIT_WINDOW+1);
        if (count($bucket) > self::RATE_LIMIT_MAX) return __('Too many scans — please slow down.');
        return null;
    }

    private function parsePayload(string $p): array
    {
        if (stripos($p,'SUQR:')===0) {
            $body = substr($p,5); $out=[];
            foreach (array_filter(array_map('trim', explode(';',$body))) as $kv){ [$k,$v]=array_map('trim', explode('=',$kv,2)+[null,null]); if($k&&$v!==null)$out[strtolower($k)]=$v; }
            $res=[]; if(!empty($out['uid'])&&is_numeric($out['uid']))$res['user_id']=(int)$out['uid']; if(!empty($out['user_id'])&&is_numeric($out['user_id']))$res['user_id']=(int)$out['user_id']; return $res;
        }
        if (str_starts_with($p,'{') && str_ends_with($p,'}')) { try { $j=json_decode($p,true,512,JSON_THROW_ON_ERROR); $r=[]; if(isset($j['user_id'])&&is_numeric($j['user_id']))$r['user_id']=(int)$j['user_id']; if(isset($j['uid'])&&is_numeric($j['uid']))$r['user_id']=(int)$j['uid']; if(!empty($j['email'])){ $u=DB::table('users')->where('email',$j['email'])->value('id'); if($u)$r['user_id']=(int)$u; } return $r; } catch(\Throwable $e){} }
        return [];
    }
    private function resolveVolunteerFromPayload(string $p): ?int
    {
        if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i',$p,$m)){ $u=DB::table('users')->where('email',$m[0])->value('id'); if($u)return (int)$u; }
        if (preg_match('/(?:uid|user)\s*[:=]\s*(\d{1,10})/i',$p,$m)) return (int)$m[1];
        if (preg_match('/^\d{1,10}$/',$p)) return (int)$p;
        return null;
    }
    private function passesGeofence(int $oppId, $lat, $lng): bool
    {
        if ($lat===null || $lng===null) return true; // allow if no location available
        $opp = DB::table('opportunities')->where('id',$oppId)->first();
        $glat = $opp->geofence_lat ?? DB::table('settings')->where('key',"opp:{$oppId}:geofence_lat")->value('value');
        $glng = $opp->geofence_lng ?? DB::table('settings')->where('key',"opp:{$oppId}:geofence_lng")->value('value');
        $radm = $opp->geofence_radius_m ?? DB::table('settings')->where('key',"opp:{$oppId}:geofence_radius_m")->value('value');
        if (!$glat || !$glng || !$radm) return true; // geofence not set
        $d = $this->haversine($lat,$lng,$glat,$glng); // meters
        return $d <= (int)$radm;
    }
    private function haversine($lat1,$lon1,$lat2,$lon2): float
    {
        $R=6371000; $φ1=deg2rad($lat1); $φ2=deg2rad($lat2); $Δφ=deg2rad($lat2-$lat1); $Δλ=deg2rad($lon2-$lon1);
        $a=sin($Δφ/2)**2 + cos($φ1)*cos($φ2)*sin($Δλ/2)**2; return $R*2*atan2(sqrt($a),sqrt(1-$a));
    }
    private function recentDuplicate(int $oppId,int $userId,string $action): bool
    {
        $since = now()->subSeconds(self::SCAN_COOLDOWN_SECONDS);
        return DB::table('qr_scans')->where('opportunity_id',$oppId)->where('user_id',$userId)->where('action',$action)->where('ok',1)->where('created_at','>=',$since)->exists();
    }
    private function logScan(int $oppId,string $action,string $payload,bool $ok,?string $note=null,?int $userId=null,$lat=null,$lng=null,$acc=null): void
    {
        DB::table('qr_scans')->insert([
          'opportunity_id'=>$oppId,'org_user_id'=>Auth::id(),'user_id'=>$userId,'payload'=>$payload,'action'=>$action,'ok'=>$ok?1:0,'note'=>$note,
          'minutes'=>null,'ip'=>request()->ip(),'user_agent'=>substr((string)request()->userAgent(),0,255),
          'created_at'=>now(),'updated_at'=>now(),
        ]);
    }
}
