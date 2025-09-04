<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        // All possible fields
        $fields = [
            // Site
            'site_name', 'homepage_hero', 'homepage_subtitle', 'homepage_image',
            // UAE PASS
            'uaepass_client_id', 'uaepass_client_secret', 'uaepass_redirect_uri',
            // Google OAuth
            'google_client_id', 'google_client_secret',
            // Facebook OAuth
            'facebook_app_id', 'facebook_app_secret',
            // WhatsApp
            'whatsapp_api_key', 'whatsapp_instance_id', 'whatsapp_number',
            // Twilio
            'twilio_sid', 'twilio_token', 'twilio_number',
            // SMTP
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass',
            // Analytics
            'ga_measurement_id', 'fb_pixel_id', 'meta_tags',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                SiteSetting::set($field, $request->$field);
            }
        }

        // Logo/image upload
        if($request->hasFile('homepage_image')) {
            $path = $request->file('homepage_image')->store('public/settings');
            SiteSetting::set('homepage_image', str_replace('public/', '', $path));
        }

        return back()->with('success', 'Settings updated.');
    }
}
