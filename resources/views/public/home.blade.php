@extends('public.layout')
@section('title','SwaEduAE — UAE Volunteer Platform')
@section('content')

<!-- Hero -->
<div class="hero-gradient text-white relative overflow-hidden">
  <div class="absolute inset-0"><div class="absolute inset-0 bg-black/20"></div></div>
  <div class="max-w-7xl mx-auto py-28 sm:py-36 px-4 sm:px-6 lg:px-8 relative z-10 text-center">
    <h1 class="text-5xl font-extrabold tracking-tight sm:text-6xl">
      <span class="block">Change Lives</span>
      <span class="block gradient-text">Together</span>
    </h1>
    <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">
      Join our vibrant community of passionate volunteers creating meaningful impact across the UAE.
    </p>
    <div class="mt-10 flex justify-center gap-4">
      <a href="{{ url('/opportunities') }}" class="px-8 py-4 rounded-xl text-lg font-semibold bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-lg">Find Opportunities</a>
      <a href="{{ url('/about') }}" class="px-8 py-4 rounded-xl text-lg font-semibold border border-white/40 text-white hover:bg-white/10">Learn More</a>
    </div>
  </div>
  <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
</div>

<!-- Stats -->
<section class="bg-gradient-to-br from-gray-50 to-white py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
      <h2 class="text-base font-semibold tracking-wide uppercase gradient-text">Our Impact</h2>
      <p class="mt-2 text-4xl font-extrabold text-gray-900">Together We&apos;ve <span class="gradient-text">Achieved</span></p>
    </div>
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
      @php $stats=[
        ['users','Dedicated Volunteers','5,000+','from-indigo-500 to-purple-600'],
        ['clock','Hours Donated','250K+','from-emerald-500 to-teal-600'],
        ['home','Communities Served','120+','from-amber-500 to-orange-600'],
        ['smile','Lives Changed','10K+','from-rose-500 to-pink-600']
      ]; @endphp
      @foreach($stats as [$icon,$label,$value,$grad])
      <div class="bg-white rounded-2xl p-8 text-center shadow-lg border border-gray-100">
        <div class="w-16 h-16 bg-gradient-to-r {{ $grad }} rounded-2xl flex items-center justify-center mx-auto shadow-lg">
          <i data-feather="{{ $icon }}" class="text-white h-8 w-8"></i>
        </div>
        <h3 class="mt-6 text-5xl font-extrabold text-gray-900">{{ $value }}</h3>
        <p class="mt-3 text-lg font-medium text-gray-600">{{ $label }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

@endsection
