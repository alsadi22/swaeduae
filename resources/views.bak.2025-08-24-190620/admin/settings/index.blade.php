@extends('admin.layout')
@section('title','Site Settings')

@section('content')
<div class="container py-4">
  <h1 class="h3 mb-3">Site Settings</h1>

  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div>@endif
  <form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="card p-3 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Site name</label>
        <input class="form-control" type="text" name="site_name" value="{{ $s['site.name'] ?? config('app.name') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Primary color (hex)</label>
        <input class="form-control" type="text" name="primary_hex" placeholder="#0ea5a6" value="{{ $s['site.primary_hex'] ?? '#0ea5a6' }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Logo</label>
        <input class="form-control" type="file" name="logo" accept="image/*">
        @if(!empty($s['site.logo_url']))<div class="mt-2"><img src="{{ $s['site.logo_url'] }}" style="max-height:60px"></div>@endif
      </div>

      <div class="col-md-6">
        <label class="form-label">Home hero image</label>
        <input class="form-control" type="file" name="hero" accept="image/*">
        @if(!empty($s['site.hero_url']))<div class="mt-2"><img src="{{ $s['site.hero_url'] }}" style="max-height:100px"></div>@endif
      </div>

      <div class="col-12">
        <label class="form-label">Social links (JSON)</label>
        <textarea class="form-control" name="social_json" rows="3" placeholder='{"twitter":"...","instagram":"...","linkedin":"..."}'>{{ json_encode($s['site.social'] ?? [], JSON_UNESCAPED_SLASHES) }}</textarea>
      </div>

      <div class="col-12">
        <button class="btn btn-primary">Save settings</button>
      </div>
    </div>
  </form>
</div>
@endsection
