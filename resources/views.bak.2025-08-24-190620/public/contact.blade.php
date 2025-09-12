@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Contact Us</h1>
  <form method="POST" action="{{ route('contact.send') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea name="message" class="form-control" rows="5" required></textarea>
    </div>
    <button class="btn btn-success">Send</button>
      @include("components.honeypot")
</form>
</div>
@endsection
