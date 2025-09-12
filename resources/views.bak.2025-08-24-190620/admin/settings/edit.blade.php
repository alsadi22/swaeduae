@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Site Assets') }}</h3>
@if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
<form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="card p-3">
  @csrf
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">{{ __('Logo') }}</label>
      <input type="file" name="logo" class="form-control">
      @if($logo)<img src="/{{ $logo }}" class="mt-2" style="max-height:60px">@endif
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Homepage Hero Image') }}</label>
      <input type="file" name="hero" class="form-control">
      @if($hero)<img src="/{{ $hero }}" class="mt-2" style="max-height:80px">@endif
    </div>
  </div>
  <div class="mt-3"><button class="btn btn-primary">{{ __('Save') }}</button></div>
</form>
@endsection
