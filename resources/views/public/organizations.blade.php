@extends('public.layout')
@section('title','Organizations')
@section('content')
<section class="py-16"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  <h1 class="text-3xl sm:text-4xl font-bold">Partner Organizations</h1>
  <p class="mt-4 text-gray-600">Browse registered organizations on SwaedUAE.</p>
  <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @for($i=1;$i<=6;$i++)
    <div class="rounded-2xl border p-6 shadow-sm">
      <h3 class="text-xl font-semibold">Organization {{ $i }}</h3>
      <p class="mt-2 text-gray-600">Description placeholder.</p>
      <a href="{{ url('/opportunities') }}" class="mt-4 inline-flex rounded-2xl border px-4 py-2 font-semibold hover:shadow transition">View opportunities</a>
    </div>
    @endfor
  </div>
</div></section>
@endsection
