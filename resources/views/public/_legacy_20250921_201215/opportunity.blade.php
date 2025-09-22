@extends('public.layout')
@section('title','Opportunity Details')
@section('content')
<section class="py-16"><div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
  <h1 class="text-3xl sm:text-4xl font-bold">Opportunity: {{ $slug ?? 'Details' }}</h1>
  <div class="mt-4 text-gray-600">This is a placeholder detail page wired to /opportunities/{idOrSlug}.</div>
  <div class="mt-8 rounded-2xl border p-6 shadow-sm">
    <div class="grid gap-3">
      <div><span class="font-semibold">Type:</span> Physical</div>
      <div><span class="font-semibold">Location:</span> TBD</div>
      <div><span class="font-semibold">Date/Time:</span> TBD</div>
    </div>
    <a href="{{ url('/contact') }}" class="mt-6 inline-flex rounded-2xl border px-5 py-3 font-semibold hover:shadow transition">Apply</a>
  </div>
</div></section>
@endsection
