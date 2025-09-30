@extends('public.layout')

@section('title', 'Contact Us')

@section('content')
<section class="section py-6">
  <div class="container stack max-w-xl">
    <header class="stack-sm">
      <span class="eyebrow">{{ __('We are here to help') }}</span>
      <h1>{{ __('Contact Us') }}</h1>
      <p class="muted">{{ __('Have a question about volunteering or partnerships? Send us a note and our team will respond shortly.') }}</p>
    </header>

    @if (session('status'))
      <div class="flash-success" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="flash-error" role="alert">
        {{ __('Please correct the highlighted fields and try again.') }}
      </div>
    @endif

    <form method="POST" action="{{ route('contact.submit') }}" class="form-card stack">
      @csrf
      <div class="input-control {{ $errors->has('name') ? 'error' : '' }}">
        <label for="name">{{ __('Full name') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required>
        <p class="form-hint">{{ __('Let us know who we should reply to.') }}</p>
        @error('name')
          <div class="error-text">{{ $message }}</div>
        @enderror
      </div>

      <div class="input-control {{ $errors->has('email') ? 'error' : '' }}">
        <label for="email">{{ __('Email address') }}</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        <p class="form-hint">{{ __('We will only use this to respond to your enquiry.') }}</p>
        @error('email')
          <div class="error-text">{{ $message }}</div>
        @enderror
      </div>

      <div class="input-control {{ $errors->has('message') ? 'error' : '' }}">
        <label for="message">{{ __('How can we help?') }}</label>
        <textarea id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
        <p class="form-hint">{{ __('Share as much detail as you can so we can direct your request to the right team member.') }}</p>
        @error('message')
          <div class="error-text">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="btn btn-primary">{{ __('Send message') }}</button>
    </form>
  </div>
</section>
@endsection
