<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
class PaymentSettingsController extends Controller {
    public function edit() {
        $stripe = (array) (\App\Models\Setting::get('payments.stripe', []) ?? []);
        $mode = $stripe['mode'] ?? env('PAYMENTS_MODE','test');
        return view()->first(['admin.settings.payments','admin.settings_plain.payments'], compact('stripe','mode'));
    }
    public function update(Request $r) {
        $data = $r->validate([
            'mode' => 'required|in:test,live',
            'key'  => 'nullable|string',
            'secret' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
        ]);
        $stripe = [
            'mode' => $data['mode'],
            'key'  => $data['key'] ?? '',
            'secret' => $data['secret'] ?? '',
            'webhook_secret' => $data['webhook_secret'] ?? '',
            'currency' => strtoupper($data['currency'] ?? 'AED'),
        ];
        \App\Models\Setting::put('payments.stripe', $stripe, encrypted: true);
        return back()->with('status','Payment settings saved.');
    }
}

