@extends('layouts.app')

@section('title','Home')

@section('content')
<h1>Welcome to SawaedUAE</h1>
<p>Small Steps to Make a Big Impact. Match your skills with volunteer opportunities across the UAE.</p>
<a href="{{ route('public.opportunities') }}">Explore opportunities</a>
@endsection
