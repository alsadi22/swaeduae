@extends('layout.layout')
@section('title','Contact | SwaedUAE')
@section('content')
<div class="container py-5">
  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
  <h1>Contact us</h1>
  <form method="POST" action="{{ route('contact.send') }}" class="mt-3">
    @csrf
    <input type="text" name="website" tabindex="-1" autocomplete="off" style="display:none">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input class="form-control" name="name" required value="{{ old('name') }}">
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" required value="{{ old('email') }}">
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>
    </div>
    <button class="btn btn-primary" type="submit">Send</button>
  </form>
</div>
@endsection
