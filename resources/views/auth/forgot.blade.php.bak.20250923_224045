@extends('layouts.app')
@section('title', __('Forgot Password').' | '.config('app.name'))
@section('page_header') <x-page-header :title="__('Forgot Password')" /> @endsection
@section('content')
<form method="POST" action="{{ route('password.email') }}" class="card p-4 shadow-sm" style="max-width:520px;margin:0 auto;">
  @csrf
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
  @if(session('reset_link'))
    <div class="alert alert-info">
      {{ __('(For testing) Reset link:') }} <a href="{{ session('reset_link') }}">{{ session('reset_link') }}</a>
    </div>
  @endif
  <div class="mb-3"><label class="form-label">{{ __('Your email') }}</label>
    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus></div>
  <button class="btn btn-teal w-100">{{ __('Send reset link') }}</button>
</form>
@endsection
