@extends('public.layout')

@section('content')
{{-- Managed SEO baseline --}}
<title>@yield('title','SwaedUAE')</title>
<meta name="description" content="@yield('meta_description','Volunteer opportunities and community programs in the UAE.')">
<link rel="canonical" href="{{ url()->current() }}"/>

<meta property="og:title" content="@yield('title','SwaedUAE')">
<meta property="og:description" content="@yield('meta_description','Volunteer opportunities and community programs in the UAE.')">
<meta property="og:image" content="{{ asset('img/og-default.jpg') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('title','SwaedUAE')">
<meta name="twitter:description" content="@yield('meta_description','Volunteer opportunities and community programs in the UAE.')">
<meta name="twitter:image" content="{{ asset('img/og-default.jpg') }}">

@endsection
