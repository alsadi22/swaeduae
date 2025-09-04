@extends('layouts.app')
@section('content')
<div class="container">
  <div class="alert alert-success mt-3">
    {{ $mode === 'in' ? __('Checked in successfully.') : __('Checked out successfully.') }}
  </div>
  <a class="btn btn-primary" href="/">{{ __('Back to home') }}</a>
</div>
@endsection
