@extends('public.layout')
@section('title','Opportunities')
@section('content')
<section class="py-16"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  <h1 class="text-3xl sm:text-4xl font-bold">Opportunities</h1>
  <p class="mt-4 text-gray-600">Explore available volunteering opportunities.</p>
  <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @for($i=1;$i<=9;$i++)
      <div class="rounded-2xl border p-6 shadow-sm">
        <div class="text-sm text-gray-500">Type: Physical</div>
        <h3 class="mt-2 text-xl font-semibold">Opportunity {{ $i }}</h3>
        <p class="mt-2 text-gray-600">Short description placeholder.</p>
        <div class="mt-4 flex gap-3">
          <a href="{{ url('/opportunities/op-'.$i) }}" class="inline-flex rounded-2xl border px-4 py-2 font-semibold hover:shadow transition">Details</a>
          <a href="{{ url('/contact') }}" class="inline-flex rounded-2xl border px-4 py-2 font-semibold hover:shadow transition">Apply</a>
        </div>
      </div>
    @endfor
  </div>
</div></section>
@endsection
