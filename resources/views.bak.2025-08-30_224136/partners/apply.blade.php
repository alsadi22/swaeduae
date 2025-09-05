@extends('layouts.app')
@section('title', __('Partner with SawaedUAE'))
@section('content')
<div class="container py-4" style="max-width:820px;">
  <h1 class="mb-3">{{ __('Partner with SawaedUAE') }}</h1>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="post" action="{{ route('partners.apply.submit') }}" novalidate>
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">{{ __('Organization Name') }}</label>
        <input name="org_name" class="form-control" value="{{ old('org_name') }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Contact Name') }}</label>
        <input name="contact_name" class="form-control" value="{{ old('contact_name') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Email') }}</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Phone') }}</label>
        <input name="phone" class="form-control" value="{{ old('phone') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Website') }}</label>
        <input name="website" class="form-control" value="{{ old('website') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Emirate') }}</label>
        <input name="emirate" class="form-control" value="{{ old('emirate') }}">
      </div>
      <div class="col-12">
        <label class="form-label">{{ __('Message') }}</label>
        <textarea name="message" rows="5" class="form-control">{{ old('message') }}</textarea>
      </div>
    </div>

    <!-- Honeypot (hidden) -->
    <div style="position:absolute; left:-9999px; top:-9999px;" aria-hidden="true">
      <label>Company Site</label>
      <input type="text" name="company_site" tabindex="-1" autocomplete="off">
    </div>

    <div class="mt-3">
      <button class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
  </form>
</div>
@endsection
