@extends('public.layout')
@section('title','About SwaedUAE')

@section('content')
<section class="py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">About SwaedUAE</h1>
    <p class="mt-4 text-lg text-gray-600">
      SwaedUAE connects volunteers with meaningful opportunities across the UAE—simple sign-up,
      QR check-ins with geofencing, automatic hour tracking, and verifiable certificates.
    </p>

    <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div class="rounded-2xl border p-6 shadow-sm">
        <h3 class="text-xl font-semibold">Our Mission</h3>
        <p class="mt-2 text-gray-600">Empower volunteers and organizations with tools that make service easy, trackable, and impactful.</p>
      </div>
      <div class="rounded-2xl border p-6 shadow-sm">
        <h3 class="text-xl font-semibold">How It Works</h3>
        <p class="mt-2 text-gray-600">Browse opportunities, check in via QR with location guardrails, and build a verified record of hours.</p>
      </div>
      <div class="rounded-2xl border p-6 shadow-sm">
        <h3 class="text-xl font-semibold">Trusted Proof</h3>
        <p class="mt-2 text-gray-600">Every certificate includes a secure QR you can verify instantly.</p>
      </div>
    </div>

    <div class="mt-10">
      <a href="{{ url('/opportunities') }}"
         class="inline-flex items-center rounded-2xl border px-5 py-3 text-base font-semibold shadow-sm hover:shadow transition">
        Explore opportunities
      </a>
    </div>
  </div>
</section>
@endsection
