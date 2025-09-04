@extends('admin.layout')
@section('admin')
<div class="container py-3">
  <h1 class="h4 mb-3">Settings</h1>
  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

  <form class="row g-3" method="POST" action="{{ route('admin.settings.save') }}">
    @csrf
    <div class="col-md-6">
      <label class="form-label">Site name</label>
      <input class="form-control" name="site_name" value="{{ $s['site_name'] ?? '' }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">Default locale</label>
      <select class="form-select" name="locale_default">
        <option value="">(inherit)</option>
        <option value="en" @selected(($s['locale_default'] ?? '')==='en')>English</option>
        <option value="ar" @selected(($s['locale_default'] ?? '')==='ar')>Arabic</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Mail From (name)</label>
      <input class="form-control" name="mail_from_name" value="{{ $s['mail_from_name'] ?? '' }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">Mail From (address)</label>
      <input class="form-control" name="mail_from_address" value="{{ $s['mail_from_address'] ?? '' }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">Plausible domain</label>
      <input class="form-control" name="plausible_domain" value="{{ $s['plausible_domain'] ?? '' }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">Sentry DSN</label>
      <input class="form-control" name="sentry_dsn" value="{{ $s['sentry_dsn'] ?? '' }}">
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Save</button>
    </div>
  </form>
</div>
@endsection
