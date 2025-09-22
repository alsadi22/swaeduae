@extends('public.layout')
@section('title','Verify Certificate')
@section('content')
<section class="py-16">
  <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl sm:text-4xl font-bold">Verify Certificate</h1>
    <div class="mt-6 rounded-2xl border p-6 shadow-sm">
      <form method="GET" action="{{ url('/certificates/verify') }}" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Certificate Code</label>
          <input name="code" value="{{ old('code', $code ?? '') }}" class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="e.g. TEST-XXXXXX" />
        </div>
        <button class="inline-flex rounded-2xl border px-5 py-3 font-semibold hover:shadow transition" type="submit">Verify</button>
      </form>
    </div>
  </div>
</section>
@endsection
