@extends('admin.layout')
@section('content')
<h3 class="mb-3">{{ __('Create Certificate') }}</h3>
<form method="post" action="{{ route('admin.certificates.store') }}" class="card p-3">
  @csrf
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">{{ __('User') }}</label>
      <select name="user_id" class="form-select" required>
        @foreach($users as $u)
          <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">{{ __('Hours') }}</label>
      <input type="number" step="0.1" min="0" name="hours" class="form-control">
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Event (optional)') }}</label>
      <select name="event_id" class="form-select">
        <option value="">{{ __('— None —') }}</option>
        @foreach($events as $e)<option value="{{ $e->id }}">{{ $e->title }}</option>@endforeach
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('Opportunity (optional)') }}</label>
      <select name="opportunity_id" class="form-select">
        <option value="">{{ __('— None —') }}</option>
        @foreach($opps as $o)<option value="{{ $o->id }}">{{ $o->title }}</option>@endforeach
      </select>
    </div>
  </div>
  <div class="mt-3"><button class="btn btn-primary">{{ __('Generate PDF') }}</button></div>
</form>
@endsection
