@extends('admin.layout')
@section('title','Settings')
@section('content')
  <h1 class="mb-3">Settings</h1>
  <form method="POST" action="{{ route('admin.settings.save') }}" class="card p-3">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Site Name</label>
        <input class="form-control" name="site_name" value="{{ old('site_name', config('app.name')) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">From Email</label>
        <input class="form-control" name="from_email" value="{{ old('from_email', env('MAIL_FROM_ADDRESS')) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Default Locale</label>
        <input class="form-control" name="default_locale" value="{{ old('default_locale', app()->getLocale()) }}">
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Save</button>
    </div>
  </form>
@endsection
