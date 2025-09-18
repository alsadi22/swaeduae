@extends('public.layout')
@section('title','Organizations')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold mb-4">Organizations</h1>
  <p class="text-gray-600 mb-6">Post opportunities, manage attendance (QR), and issue certificates.</p>
  <div class="flex gap-3">
    <a href="/contact" class="px-5 py-3 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Partner with us</a>
    <a href="/login" class="px-5 py-3 rounded-lg border border-gray-300 hover:bg-gray-50">Organization Sign-in</a>
  </div>
</div>
@endsection
