@extends('admin.layout')

@section('title', __('Finalize Certificates'))

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('Finalize Certificates') }}</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <p class="text-muted">
        {{ __('Generate PDF+QR certificates for all attendees of the specified Opportunity ID. Requires attendance records.') }}
      </p>
      <form method="POST" action="{{ route('admin.tools.finalizeCerts.post') }}" class="row g-3">
        @csrf
        <div class="col-md-4">
          <label for="opportunity_id" class="form-label">{{ __('Opportunity ID') }}</label>
          <input type="number" min="1" class="form-control" id="opportunity_id" name="opportunity_id"
                 value="{{ old('opportunity_id', request('opportunity_id')) }}" required>
        </div>
        <div class="col-12">
          <button class="btn btn-success">{{ __('Finalize & Generate Certificates') }}</button>
          <a href="{{ url('/admin') }}" class="btn btn-outline-secondary">{{ __('Back to Admin') }}</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
