@extends('public.layout')
@section('title', 'Opportunity Details')
@section('content')
<section class="py-16">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <a href="{{ url('/opportunities') }}" class="inline-flex items-center rounded-xl border px-3 py-1.5 text-sm hover:shadow transition">&larr; All opportunities</a>
    <h1 class="mt-6 text-3xl sm:text-4xl font-bold tracking-tight">
      {{ isset($slug) ? ucwords(str_replace(['-','_'],' ', $slug)) : 'Opportunity' }}
    </h1>

    <div class="mt-6 rounded-2xl border p-6 shadow-sm">
      <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <dt class="text-sm text-gray-500">Type</dt>
          <dd class="text-base font-medium">Physical</dd>
        </div>
        <div>
          <dt class="text-sm text-gray-500">Location</dt>
          <dd class="text-base font-medium">To be announced</dd>
        </div>
        <div>
          <dt class="text-sm text-gray-500">Date/Time</dt>
          <dd class="text-base font-medium">TBD</dd>
        </div>
        <div>
          <dt class="text-sm text-gray-500">Reference</dt>
          <dd class="text-base font-medium">{{ $slug ?? '—' }}</dd>
        </div>
      </dl>

      <div class="mt-6 text-gray-700">
        <p>This is a placeholder details page wired to <code>/opportunities/{idOrSlug}</code>. Replace with real data when backend is ready.</p>
      </div>

      <div class="mt-8 flex gap-3">
        <a href="{{ url('/contact') }}" class="inline-flex items-center rounded-2xl border px-5 py-3 font-semibold hover:shadow transition">
          Apply / Contact
        </a>
        <a href="{{ url('/opportunities') }}" class="inline-flex items-center rounded-2xl border px-5 py-3 font-semibold hover:shadow transition">
          Back to list
        </a>
      </div>
    </div>
  </div>
</section>
@endsection
