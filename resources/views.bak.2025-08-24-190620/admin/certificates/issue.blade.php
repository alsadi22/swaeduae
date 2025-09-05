@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('swaed.issue_certificate') }}</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" class="card border-0 shadow-sm p-3">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">{{ __('swaed.user_email') }}</label>
        <input type="email" name="user_email" value="{{ old('user_email') }}" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('swaed.event_id') }}</label>
        <input type="number" name="event_id" value="{{ old('event_id') }}" class="form-control" placeholder="{{ __('swaed.optional') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">{{ __('swaed.code') }}</label>
        <input type="text" name="code" value="{{ old('code') }}" class="form-control" placeholder="{{ __('swaed.auto') }}">
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">{{ __('swaed.issue') }}</button>
      <a class="btn btn-outline-secondary" href="{{ route('admin.certificates.index') }}">{{ __('swaed.back') }}</a>
    </div>
  </form>
</div>
@endsection
