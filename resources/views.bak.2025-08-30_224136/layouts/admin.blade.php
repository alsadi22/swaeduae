@php($hero = $hero ?? []);
@extends('layouts.argon')
@section('content')
  @include('partials.nav.admin')
  <div class="container py-4">@yield('page')</div>
@endsection
