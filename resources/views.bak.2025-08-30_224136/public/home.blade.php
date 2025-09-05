@extends('public.layout-travelpro')

@section('content')
<section class="container py-5">
  <h1 class="display-5 fw-bold">Welcome to SwaedUAE</h1>
  <p class="lead">We’re now live. Explore the core pages while we keep building.</p>
  <p class="d-flex gap-3">
    <a href="{{ route('services') }}">Services</a> ·
    <a href="{{ route('about') }}">About</a> ·
    <a href="{{ route('contact.get') }}">Contact</a>
  </p>
</section>
@endsection
