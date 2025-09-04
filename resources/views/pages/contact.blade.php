@extends('layouts.app')
@section('content')
<div class="container py-4">
  @if(session('status')==='ok')
    <div class="alert alert-success">Thanks — we received your message.</div>
  @endif
  <h1 class="mb-3">{{ __('Contact') }}</h1>
  <form method="POST" action="{{ route('contact.send') }}">
    @csrf
    <input type="hidden" name="website" value="">
    <div class="mb-3">
      <label class="form-label">{{ __('Name') }}</label>
      <input name="name" class="form-control" value="{{ old('name') }}" required maxlength="100">
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Email') }}</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="150">
      @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Subject') }}</label>
      <input name="subject" class="form-control" value="{{ old('subject') }}" maxlength="150">
      @error('subject')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Message') }}</label>
      <textarea name="message" class="form-control" rows="6" required minlength="10" maxlength="2000">{{ old('message') }}</textarea>
      @error('message')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary" type="submit">{{ __('Send') }}</button>
  </form>
</div>
@endsection