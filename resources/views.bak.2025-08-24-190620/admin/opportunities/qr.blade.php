@extends('admin.layout')
@section('content')
  <h3 class="mb-3">{{ __('QR Check-in / Check-out') }}</h3>
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card p-3">
        <h5 class="mb-2">{{ __('Check-in') }}</h5>
        {!! QrCode::size(220)->generate($checkinUrl) !!}
        <div class="mt-2"><code>{{ $checkinUrl }}</code></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <h5 class="mb-2">{{ __('Check-out') }}</h5>
        {!! QrCode::size(220)->generate($checkoutUrl) !!}
        <div class="mt-2"><code>{{ $checkoutUrl }}</code></div>
      </div>
    </div>
  </div>

  <div class="mt-3 d-flex gap-2">
    <form method="post" action="{{ route('admin.attendance.qr.reset',$opp->id) }}" onsubmit="return confirm('{{ __('Regenerate tokens? Printed QR codes will stop working.') }}')">
      @csrf <button class="btn btn-outline-secondary">{{ __('Regenerate Tokens') }}</button>
    </form>
    <a class="btn btn-outline-primary" href="{{ route('admin.attendance.qr.finalize',$opp->id) }}">{{ __('Finalize Hours') }}</a>
    <a class="btn btn-success" href="{{ route('admin.attendance.qr.issue',$opp->id) }}">{{ __('Complete & Issue Certificates') }}</a>
  </div>
@endsection
