@extends('org.layout')
@section('content')
<div class="container py-4">
  <h1 class="h5 mb-3">{{ __('KYC / Organization License') }}</h1>
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
  <div class="card shadow-sm">
    <div class="card-body">
      <p class="text-muted">{{ __('Upload your organization’s trade license or approval letter.') }}</p>
      <form method="POST" action="{{ route('org.kyc.update') }}" enctype="multipart/form-data" class="d-flex gap-2">
        @csrf
        <input type="file" name="license" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
        <button class="btn btn-primary">{{ __('Upload') }}</button>
      </form>
      @if(!empty($kyc))
        <hr>
        <div>{{ __('Current status') }}: <strong>{{ ucfirst($kyc->status) }}</strong></div>
        @if($kyc->file_path) <div><a href="{{ $kyc->file_path }}" target="_blank">{{ __('View file') }}</a></div>@endif
      @endif
    </div>
  </div>
</div>
@endsection
