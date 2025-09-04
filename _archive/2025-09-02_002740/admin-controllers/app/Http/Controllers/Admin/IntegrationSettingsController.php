<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use App\Models\Setting;
class IntegrationSettingsController extends Controller {
  public function edit() {
    $vals = (array) (Setting::get('integrations.social', []) ?? []);
    return view()->first(['admin.settings.api','admin.settings_plain.api'], compact('vals'));
  }
  public function update(Request $r) {
    $data = $r->validate([
      'google_client_id'=>'nullable|string','google_client_secret'=>'nullable|string',
      'facebook_app_id'=>'nullable|string','facebook_app_secret'=>'nullable|string',
    ]);
    Setting::put('integrations.social', $data, encrypted:true);
    return back()->with('status',__('Saved'));
  }
}

