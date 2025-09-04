@extends('layouts.app')
@section('title', __('My Profile'))
@section('content')
<div class="container py-4" style="max-width:680px;">
  <h1 class="mb-3">{{ __('My Profile') }}</h1>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="post" action="{{ route('vol.profile.update') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">{{ __('Name') }}</label>
      <input name="name" class="form-control" value="{{ old('name', $u->name) }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Email') }}</label>
      <input class="form-control" value="{{ $u->email }}" disabled>
    </div>
    <button class="btn btn-primary">{{ __('Save Changes') }}</button>
  </form>
</div>
@endsection
