@extends('public.layout')
@section('title', 'Rescue')
@section('content')
<!doctype html>
<html lang="{{ str_replace("_","-",app()->getLocale()) }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SwaedUAE</title>
  <link rel="stylesheet" href="{{ asset("assets/app.css") }}">
</head>
<body>
  <div class="container" style="max-width:1100px;margin:0 auto;padding:1rem">
    <h1>سواعد الإمارات</h1>
    <p>Connecting volunteers with opportunities across the UAE.</p>
    <p><a href="/opportunities">Opportunities</a> · <a href="/events">Events</a> · <a href="/contact">Contact</a></p>
    <p>© {{ date("Y") }} SwaedUAE · <a href="/privacy">Privacy</a> · <a href="/terms">Terms</a></p>
  </div>
</body>
</html>
@endsection
