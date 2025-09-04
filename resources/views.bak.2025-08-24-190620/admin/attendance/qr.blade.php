@extends('admin.layout')

@section('content')
  <h3 class="mb-3">{{ __('QR Check-in / Check-out') }} — #{{ $opp->id }}</h3>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card p-3 text-center">
        <h5>{{ __('Check-in') }}</h5>
        <div class="my-2">{!! QrCode::size(220)->generate($checkinUrl) !!}</div>
        <div class="small text-muted">{{ $checkinUrl }}</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3 text-center">
        <h5>{{ __('Check-out') }}</h5>
        <div class="my-2">{!! QrCode::size(220)->generate($checkoutUrl) !!}</div>
        <div class="small text-muted">{{ $checkoutUrl }}</div>
      </div>
    </div>
  </div>

  <div class="card p-3 mt-3">
    <form method="get" action="{{ route('admin.attendance.index') }}" class="d-inline">
      <input type="hidden" name="opportunity_id" value="{{ $opp->id }}">
      <button class="btn btn-outline-secondary">{{ __('View Attendance List') }}</button>
    </form>
    <form method="post" action="{{ route('admin.attendance.finalize',$opp->id) }}" class="d-inline ms-2">
      @csrf
      <button class="btn btn-outline-primary">{{ __('Finalize Hours (Compute)') }}</button>
    </form>
    <form method="post" action="{{ route('admin.opportunities.complete',$opp->id) }}" class="d-inline ms-2" onsubmit="return confirm('{{ __('Mark completed and issue certificates?') }}')">
      @csrf
      <button class="btn btn-success">{{ __('Complete & Issue Certificates') }}</button>
    </form>
  </div>
@endsection
