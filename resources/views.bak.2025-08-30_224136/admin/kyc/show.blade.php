@extends('admin.layout')
@section('title', __('Review KYC'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 m-0">{{ __('KYC Request') }} #{{ $kyc->id }}</h1>
  <div>
    <form method="POST" action="{{ route('admin.kyc.approve',$kyc->id) }}" class="d-inline">@csrf
      <button class="btn btn-success">{{ __('Approve') }}</button>
    </form>
    <form method="POST" action="{{ route('admin.kyc.reject',$kyc->id) }}" class="d-inline">@csrf
      <button class="btn btn-danger">{{ __('Reject') }}</button>
    </form>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="mb-2"><strong>{{ __('User') }}:</strong> {{ $kyc->user->name ?? ('#'.$kyc->user_id) }}</div>
    <div class="mb-2"><strong>{{ __('Status') }}:</strong> {{ ucfirst($kyc->status) }}</div>
    <div class="mb-2"><strong>{{ __('Submitted') }}:</strong> {{ $kyc->created_at->toDateTimeString() }}</div>
    <hr>
    <pre class="small" style="white-space:pre-wrap">{{ json_encode($kyc->data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    @if($kyc->document_path)
      <a class="btn btn-outline-secondary" href="{{ route('admin.kyc.download',$kyc->id) }}">{{ __('Download Document') }}</a>
    @endif
  </div>
</div>
@endsection
