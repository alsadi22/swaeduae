@extends('public.layout-travelpro')
@section('content')
<div class="container py-5">
  <h1 class="h3 mb-3">Contact Us</h1>
  @if (session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
  @if ($errors->any()) <div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div> @endif

  <form method="POST" action="{{ route('contact.submit') }}" class="mt-3">
    @csrf
    <input type="text" name="website" style="display:none" autocomplete="off">
    <div class="mb-3">
      <label class="form-label">Your Name</label>
      <input class="form-control" name="name" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea class="form-control" name="message" rows="6" required>{{ old('message') }}</textarea>
    </div>
    <button class="btn btn-primary" type="submit">Send</button>
  </form>
</div>
@endsection
