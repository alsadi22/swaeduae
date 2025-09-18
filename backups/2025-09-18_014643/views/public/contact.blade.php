@extends('public.layout')

@section('title', 'Contact Us')

@section('content')
  <h1 class="mb-3">Contact Us</h1>

  @if (session('status'))
    <div class="mb-3" role="status">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('contact.submit') }}" class="space-y-3">
    @csrf
    <div class="mb-3">
      <label for="name">Name</label>
      <input id="name" name="name" type="text" value="{{ old('name') }}" required class="w-full">
      @error('name') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full">
      @error('email') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
      <label for="message">Message</label>
      <textarea id="message" name="message" rows="6" required class="w-full">{{ old('message') }}</textarea>
      @error('message') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary">Send</button>
  </form>
@endsection
