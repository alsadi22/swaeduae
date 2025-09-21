@extends('public.layout')
@section('title','Verify Certificate')
@section('content')
<section class="py-16">
  <div class="max-w-xl mx-auto bg-white text-slate-900 rounded-2xl shadow-xl p-6">
    <h1 class="text-xl font-semibold mb-4">Verify Certificate</h1>
    <form method="GET" action="{{ url('/certificates/verify') }}">
      <label class="block text-sm font-medium text-slate-700 mb-1">Code</label>
      <input name="code" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('code', request('code', $code ?? '')) }}">
      <button class="mt-4 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Verify</button>
    </form>
  </div>
</section>
@endsection
