@extends('public.layout')
@section('title','Page not found — '.config('app.name'))
@section('page')
  <div class="py-16 text-center">
    <h1 class="text-4xl font-semibold mb-4">404</h1>
    <p class="mb-6">We couldn’t find that page.</p>
    <a href="{{ url('/') }}" class="underline">Go home</a>
  </div>
@endsection
