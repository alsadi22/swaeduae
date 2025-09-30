@extends('public.layout')

@section('title', __('Verify a Certificate'))

@section('content')
<section class="section py-6">
  <div class="container stack max-w-xl">
    <header class="stack-sm">
      <span class="eyebrow">{{ __('QR & code lookup') }}</span>
      <h1>{{ __('Verify a SwaedUAE certificate') }}</h1>
      <p class="muted">{{ __('Enter the verification code that appears next to the QR code on the certificate to confirm its authenticity.') }}</p>
    </header>

    @php
      $raw = $result ?? null;
      $payload = is_array($raw) ? $raw : (is_object($raw) ? (array) $raw : []);
      $hasExplicit = array_key_exists('ok', $payload) || array_key_exists('valid', $payload) || array_key_exists('success', $payload);
      $ok = $hasExplicit ? ($payload['ok'] ?? $payload['valid'] ?? $payload['success']) : (is_bool($raw) ? $raw : null);
      $message = $payload['message'] ?? null;
      $holder = $payload['name'] ?? $payload['holder'] ?? $payload['volunteer'] ?? $payload['volunteer_name'] ?? null;
      $programme = $payload['opportunity'] ?? $payload['event'] ?? $payload['programme'] ?? $payload['event_title'] ?? null;
      $hours = $payload['hours'] ?? $payload['duration'] ?? null;
      $codeValue = old('code', $code ?? request('code'));
    @endphp

    <form method="GET" action="{{ route('qr.verify') }}" class="form-card stack">
      <div class="input-control {{ $errors->has('code') ? 'error' : '' }}">
        <label for="code">{{ __('Verification code') }}</label>
        <input id="code" name="code" type="text" inputmode="text" autocomplete="off" value="{{ $codeValue }}" placeholder="{{ __('e.g. SWD-2024-0001') }}" required>
        <p class="form-hint">{{ __('You will find this near the QR code. Include any dashes exactly as printed.') }}</p>
        @error('code')
          <div class="error-text">{{ $message }}</div>
        @enderror
      </div>

      <button class="btn btn-primary" type="submit">{{ __('Check certificate') }}</button>
    </form>

    @if (!is_null($ok))
      <div class="callout {{ $ok ? 'callout-success' : 'callout-error' }} stack-xs">
        <strong>{{ $ok ? __('Certificate verified') : __('Certificate not found') }}</strong>
        @if ($message)
          <p>{{ $message }}</p>
        @elseif ($ok)
          <p>{{ __('This certificate is valid and was issued by SwaedUAE.') }}</p>
        @else
          <p>{{ __('We could not match this code. Double-check the code and try again, or contact our team for support.') }}</p>
        @endif

        @if ($ok && ($holder || $programme || $hours))
          <dl class="meta-grid">
            @if ($holder)
              <span><strong>{{ __('Volunteer:') }}</strong> {{ $holder }}</span>
            @endif
            @if ($programme)
              <span><strong>{{ __('Programme:') }}</strong> {{ $programme }}</span>
            @endif
            @if ($hours)
              <span><strong>{{ __('Hours credited:') }}</strong> {{ $hours }}</span>
            @endif
          </dl>
        @endif
      </div>
    @elseif ($codeValue)
      <div class="callout callout-error">
        <strong>{{ __('We could not confirm this code.') }}</strong>
        <p>{{ __('Please ensure you entered the characters exactly as they appear and try again.') }}</p>
      </div>
    @endif
  </div>
</section>
@endsection
