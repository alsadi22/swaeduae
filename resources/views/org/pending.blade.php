@extends('public.layout-travelpro')
@section('title','Organization Pending Approval')
@section('content')
<section class="container py-5">
  <h1 class="mb-3">Pending Approval</h1>
  <p>Your organization application is under review. You will receive an email once approved.</p>
  <p><a class="btn btn-primary" href="{{ url('/') }}">Back to home</a></p>
  @if(session('status'))<p class="text-success mt-3">{{ session('status') }}</p>@endif
</section>
@endsection
