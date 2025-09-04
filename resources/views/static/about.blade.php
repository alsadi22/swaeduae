@extends('layouts.app')

@section('title','About Us')
@section('content')
  <div class="container py-5">
    @if (view()->exists('about'))
      @include('about')
    @else
      <h1 class="mb-3">About SwaedUAE</h1>
      <p>This is a temporary About page. Replace later with your real content
         (e.g. create <code>resources/views/pages/about.blade.php</code>).</p>
    @endif
  </div>
@endsection
