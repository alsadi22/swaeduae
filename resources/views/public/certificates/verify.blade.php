@extends('public.layout')
@section('title','Verify Certificate')
@section('content')
  <section class="mx-auto max-w-3xl px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">Verify Certificate</h1>
    <form method="GET" action="{{ url('/certificates/verify') }}" class="grid gap-4">
      <label class="block">
        <span class="block mb-2 font-medium">Code</span>
        <input name="code" value="{{ request('code') }}" class="w-full rounded-xl border px-4 py-3" placeholder="Enter certificate code">
      </label>
      <button class="btn-primary">Verify</button>
    </form>

    @if(request('code'))
      <div class="mt-6 card">
        <p class="text-slate-700">
          Searching for code: <strong>{{ request('code') }}</strong> …
        </p>
      </div>
    @endif
  </section>
@endsection
