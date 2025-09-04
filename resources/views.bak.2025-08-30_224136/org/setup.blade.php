@extends('org.layout')
@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('swaed.org_setup') ?? 'Organization Setup' }}</h1>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('org.setup.store') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">{{ __('swaed.organization') }}</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('swaed.license_no') ?? 'License No.' }}</label>
          <input type="text" name="license_no" class="form-control">
        </div>
        <button class="btn btn-primary">{{ __('swaed.save') ?? 'Save' }}</button>
      </form>
    </div>
  </div>
</div>
@endsection
