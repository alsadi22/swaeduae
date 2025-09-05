@extends('layouts.org')
@section('content')
  @include('partials.nav.org')
  <div class="container py-4">
    <h1 class="h4 mb-3">Organization Dashboard</h1>
    <p class="text-muted mb-0">Welcome, {{ auth()->user()?->name ?? 'Org' }}.</p>
  </div>
@endsection
