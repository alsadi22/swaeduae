@extends('admin.layout')
@section('title', __('Certificate'))

@section('content')
<div class="container-fluid py-4">
  <h1 class="mb-3" style="font-weight:700;">{{ __('Certificate') }} #{{ $c->id }}</h1>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card p-3">
        <div><strong>{{ __('Code') }}:</strong> {{ $c->code }}</div>
        <div><strong>{{ __('User') }}:</strong> {{ $c->user_name }} ({{ $c->email }})</div>
        <div><strong>{{ __('Opportunity') }}:</strong> {{ $c->opportunity_title ?? '-' }}</div>
        <div><strong>{{ __('Status') }}:</strong> @if($c->revoked_at) <span class="badge bg-danger">{{ __('Revoked') }}</span> @else <span class="badge bg-success">{{ __('Valid') }}</span> @endif</div>
        <div><strong>{{ __('File') }}:</strong> @if($c->file_path)<a href="{{ url('/'.$c->file_path) }}" target="_blank">{{ __('Download') }}</a>@endif</div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <form method="POST" action="{{ url('/admin/certificates/'.$c->id.'/resend-email') }}">@csrf<button class="btn btn-primary">{{ __('Resend Email') }}</button></form>
        <a class="btn btn-outline-success" target="_blank" href="{{ url('/admin/certificates/'.$c->id.'/whatsapp') }}">{{ __('WhatsApp') }}</a>
        @if(!$c->revoked_at)
          <form method="POST" action="{{ url('/admin/certificates/'.$c->id.'/revoke') }}">@csrf<button class="btn btn-danger">{{ __('Revoke') }}</button></form>
        @endif
        <form method="POST" action="{{ url('/admin/certificates/'.$c->id.'/reissue') }}">@csrf<button class="btn btn-warning">{{ __('Reissue') }}</button></form>
      </div>
    </div>

    <div class="col-lg-6">
      @if($c->file_path)
      <div class="card p-2">
        <object data="{{ url('/'.$c->file_path) }}" type="application/pdf" style="width:100%;height:600px;">
          <p class="p-3">{{ __('PDF preview not available. Download instead.') }}</p>
        </object>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
