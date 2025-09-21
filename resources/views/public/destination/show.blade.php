@extends('public.layout')
@section('title','Destination: {{ $slug ?? 'details' }}')
@section('content')
<section class="py-16"><div class="wrap">
@extends('layout.layout')
@section('content')
<div class="container py-5"><h1>Destination: {{ $slug ?? 'details' }}</h1></div>
@endsection

</div></section>
@endsection
