@extends('layouts.app')

@section('title','Register Organization')
@section('content')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
<div class="auth-wrap py-5">
  <div class="container">
    <div class="auth-card mx-auto row g-0">
      <div class="auth-left col-12">
        <div class="auth-title">{{ __('Register Your Organization') }}</div>
        <div class="auth-sub">{{ __('Business account — full details required') }}</div>

        <form method="POST" action="{{ route('register.organization.store') }}" class="mt-3">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('Organization Name') }}</label>
              <input type="text" name="organization_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Trade License Number') }}</label>
              <input type="text" name="trade_license_number" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Business Email') }}</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Password') }}</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Phone') }}</label>
              <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Website') }}</label>
              <input type="url" name="website" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Emirate') }}</label>
              <input type="text" name="emirate" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('City') }}</label>
              <input type="text" name="city" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">{{ __('Address') }}</label>
              <input type="text" name="address" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Contact Person Name') }}</label>
              <input type="text" name="contact_person_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Contact Person Email') }}</label>
              <input type="email" name="contact_person_email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Contact Person Phone') }}</label>
              <input type="text" name="contact_person_phone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Sector / Category') }}</label>
              <input type="text" name="sector" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">{{ __('About / Description') }}</label>
              <textarea name="description" rows="3" class="form-control"></textarea>
            </div>
            <div class="col-12 form-check my-2">
              <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
              <label class="form-check-label" for="terms">{{ __('I confirm the above details are accurate and agree to the terms.') }}</label>
            </div>
          </div>

          <button class="btn btn-success w-100 py-2 mt-3" type="submit">{{ __('Create Organization Account') }}</button>
          <div class="text-center mt-3">
            <a href="{{ route('login.organization') }}">{{ __('Back to login') }}</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
