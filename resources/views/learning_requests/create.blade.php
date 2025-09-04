@extends('layouts.app')
@section('content')
<div class="container">
  <h3 class="mb-3">{{ __('Request a Course / Class') }}</h3>
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
  <form method="post" action="{{ route('learning.store') }}" class="card p-3">
    @csrf
    <div class="mb-3">
      <label class="form-label">{{ __('Title') }}</label>
      <input name="title" class="form-control" required placeholder="e.g., First Aid Basics Workshop">
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Details') }}</label>
      <textarea name="details" class="form-control" rows="5" placeholder="{{ __('Describe what you would like to learn') }}"></textarea>
    </div>
    <button class="btn btn-primary">{{ __('Submit Request') }}</button>
  </form>
</div>
@endsection
