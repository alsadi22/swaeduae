@extends('public.layout')
@section('title','Volunteers')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold mb-4">Volunteers</h1>
  <p class="text-gray-600 mb-6">Join opportunities, track your hours, and earn certificates.</p>
  @auth
    <a href="/my/profile" class="inline-block px-5 py-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Go to My Profile</a>
  @else
    <a href="/login" class="inline-block px-5 py-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Sign in to start</a>
  @endauth
</div>
@endsection
