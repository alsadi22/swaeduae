@extends('public.layout')
@section('title', 'Show')
@section('content')
<div class="container py-5"><h1>Destination: {{ $slug ?? 'details' }}</h1></div>
@endsection
