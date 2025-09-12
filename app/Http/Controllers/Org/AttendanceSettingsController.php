<?php
namespace App\Http\Controllers\Org;
use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceSettingsController extends Controller
{
    public function edit(Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);
        $geo = [
          'lat' => $opportunity->geofence_lat ?? (DB::table('settings')->where('key',"opp:{$opportunity->id}:geofence_lat")->value('value')),
          'lng' => $opportunity->geofence_lng ?? (DB::table('settings')->where('key',"opp:{$opportunity->id}:geofence_lng")->value('value')),
          'radius' => $opportunity->geofence_radius_m ?? (DB::table('settings')->where('key',"opp:{$opportunity->id}:geofence_radius_m")->value('value')),
        ];
        return view('org.attendance.settings', compact('opportunity','geo'));
    }
    public function update(Request $request, Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);
        $data = $request->validate([
            'lat' => ['nullable','numeric','between:-90,90'],
            'lng' => ['nullable','numeric','between:-180,180'],
            'radius' => ['nullable','integer','min:50','max:2000'],
        ]);
        if (Schema::hasColumn('opportunities','geofence_lat')) {
            DB::table('opportunities')->where('id',$opportunity->id)->update([
                'geofence_lat' => $data['lat'] ?? null,
                'geofence_lng' => $data['lng'] ?? null,
                'geofence_radius_m' => $data['radius'] ?? null,
                'updated_at'=>now(),
            ]);
        } else {
            foreach (['lat','lng','radius'] as $k) {
              DB::table('settings')->updateOrInsert(
                ['key'=>"opp:{$opportunity->id}:geofence_{$k}"],
                ['value'=>$data[$k] ?? null, 'updated_at'=>now(),'created_at'=>now()]
              );
            }
        }
        return back()->with('status', __('Attendance settings saved'));
    }
}
