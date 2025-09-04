@extends('layouts.app')

@section('title', __('Contact'))
@section('content')
@include('partials.page_header', ['title' => __('Contact Us'), 'subtitle' => __('We usually respond within 1–2 business days.')])

<div class="container my-4">
  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form method="POST" action="{{ route('contact.submit') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">{{ __('Your Name') }}</label>
          <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('Email Address') }}</label>
          <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('Message') }}</label>
          <textarea name="message" rows="6" class="form-control" required>{{ old('message') }}</textarea>
        </div>
        <button class="btn btn-primary">{{ __('Send Message') }}</button>
      </form>
    </div>
  </div>
</div>
@endsection
