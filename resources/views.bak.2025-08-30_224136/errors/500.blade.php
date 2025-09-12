@extends('public.layout')
@section('title','Something went wrong — '.config('app.name'))
@section('page')
  <div class="py-16 text-center">
    <h1 class="text-4xl font-semibold mb-4">500</h1>
    <p class="mb-6">An unexpected error occurred. Please try again later.</p>
    <a href="{{ url('/') }}" class="underline">Go home</a>
  </div>
@endsection
