@extends("layouts.admin-argon")
<!doctype html><html lang="en"><head><meta charset="utf-8"><title>API Integrations</title>
<meta name="viewport" content="width=device-width, initial-scale=1"></head><body style="font:16px system-ui;padding:20px;max-width:900px;margin:0 auto">
<h1>API Integrations</h1>
@if(session('status'))<p style="color:green">{{ session('status') }}</p>@endif
<form method="post" action="{{ route('admin.settings.api.save') }}" style="display:grid;gap:12px;max-width:700px">
  @csrf
  <label>Google Client ID <input name="google_client_id" value="{{ $vals['google_client_id'] ?? '' }}" style="width:100%"></label>
  <label>Google Client Secret <input name="google_client_secret" value="{{ $vals['google_client_secret'] ?? '' }}" style="width:100%"></label>
  <label>Facebook App ID <input name="facebook_app_id" value="{{ $vals['facebook_app_id'] ?? '' }}" style="width:100%"></label>
  <label>Facebook App Secret <input name="facebook_app_secret" value="{{ $vals['facebook_app_secret'] ?? '' }}" style="width:100%"></label>
  <button>Save</button>
</form>
</body></html>
