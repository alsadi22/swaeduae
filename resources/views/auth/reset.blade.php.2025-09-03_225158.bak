@extends('layouts.app')
@section('title', __('Reset Password').' | '.config('app.name'))
@section('page_header') <x-page-header :title="__('Reset Password')" /> @endsection
@section('content')
<form method="POST" action="{{ route('password.update') }}" class="card p-4 shadow-sm" style="max-width:520px;margin:0 auto;">
  @csrf
  <input type="hidden" name="token" value="{{ $token }}">
  <div class="mb-3"><label class="form-label">{{ __('Email') }}</label>
    <input type="email" name="email" class="form-control" value="{{ $email }}" required></div>
  <div class="mb-3"><label class="form-label">{{ __('New password') }}</label>
    <input type="password" name="password" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">{{ __('Confirm password') }}</label>
    <input type="password" name="password_confirmation" class="form-control" required></div>
  <button class="btn btn-teal w-100">{{ __('Update password') }}</button>
</form>
@endsection
