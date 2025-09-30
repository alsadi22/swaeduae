@extends('public.layout')

@section('title', __('Verify a Certificate'))

@section('content')
<section class="section py-6">
  <div class="container stack max-w-xl">
    <header class="stack-sm">
      <span class="eyebrow">{{ __('Legacy certificate lookup') }}</span>
      <h1>{{ __('Check a certificate code') }}</h1>
      <p class="muted">{{ __('Use the form below to confirm the authenticity of a certificate issued by SwaedUAE.') }}</p>
    </header>

    @php
      $codeValue = old('code', request('code'));
      $isValid = isset($certificate);
    @endphp

    <form method="GET" action="{{ route('certificates.verify.form') }}" class="form-card stack">
      <div class="input-control">
        <label for="code">{{ __('Verification code') }}</label>
        <input id="code" name="code" type="text" value="{{ $codeValue }}" placeholder="{{ __('e.g. SWD-2024-0001') }}" required>
        <p class="form-hint">{{ __('Enter the full alphanumeric code printed on the certificate. Include dashes if shown.') }}</p>
      </div>
      <button class="btn btn-primary" type="submit">{{ __('Verify now') }}</button>
    </form>

    @if ($isValid)
      <div class="callout callout-success stack-xs">
        <strong>{{ __('Certificate verified') }}</strong>
        <p>{{ __('This code matches a certificate that we have on record.') }}</p>
        <dl class="meta-grid">
          <span><strong>{{ __('Holder:') }}</strong> {{ $certificate->holder_name ?? '—' }}</span>
          <span><strong>{{ __('Hours:') }}</strong> {{ $certificate->hours ?? '—' }}</span>
          <span><strong>{{ __('Certificate #:') }}</strong> {{ $certificate->code ?? request('code') }}</span>
        </dl>
      </div>
    @elseif($codeValue)
      <div class="callout callout-error stack-xs">
        <strong>{{ __('We could not find that certificate') }}</strong>
        <p>{{ __('Please double-check the code and try again. You can also scan the QR code on the certificate for instant verification.') }}</p>
      </div>
    @endif
  </div>
</section>
@endsection
