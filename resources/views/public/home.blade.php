@extends('public.layout')
@section('content')
@php
  $stats = [
    'opportunities' => \DB::table('opportunities')->count(),
    'volunteers'    => \DB::table('users')->count(),
    'certificates'  => \DB::table('certificates')->count(),
  ];
@endphp
<style>
  .hero{background:#0b1220;color:#fff}
  .hero .wrap{max-width:1100px;margin:0 auto;padding:64px 20px}
  .cta{display:inline-block;margin-top:16px;padding:.7rem 1rem;background:#1e66f5;color:#fff;border-radius:10px;text-decoration:none}
  .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px}
  .muted{color:#64748b}
</style>

<section class="hero">
  <div class="wrap">
    <h1 class="mb-2">SwaedUAE — Volunteer & Serve</h1>
    <p class="muted">Find opportunities, serve your community, and earn verified certificates.</p>
    <a class="cta" href="{{ url('/opportunities') }}">Browse Opportunities</a>
  </div>
</section>

<section class="container py-5">
  <div class="grid">
    <div class="card"><h3 class="m-0">{{ $stats['opportunities'] }}</h3><div class="muted">Opportunities</div></div>
    <div class="card"><h3 class="m-0">{{ $stats['volunteers'] }}</h3><div class="muted">Volunteers</div></div>
    <div class="card"><h3 class="m-0">{{ $stats['certificates'] }}</h3><div class="muted">Certificates Issued</div></div>
  </div>
</section>

<section class="container pb-5">
  <h2 class="h5 mb-3">How it works</h2>
  <ol class="muted">
    <li>Browse opportunities and apply.</li>
    <li>Get approved and participate.</li>
    <li>Hours are recorded; download your certificate.</li>
  </ol>
</section>
@endsection
