@extends('layouts.argon')
@section('content')
  @include('partials.nav.org')
  <div class="container py-4">@yield('page')</div>
@endsection
