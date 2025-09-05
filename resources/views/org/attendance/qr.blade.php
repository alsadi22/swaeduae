@extends('org.layout')
@section('title','Attendance QR')
@section('content')
<div class="container py-5">
  <h3 class="mb-3">Attendance QR</h3>
  @if($qrSvg)
    <div class="p-3 border rounded bg-white">{!! $qrSvg !!}</div>
    <p class="mt-3"><strong>Scan URL:</strong> <a href="{{ $scanUrl }}" target="_blank">{{ $scanUrl }}</a></p>
  @else
    <div class="alert alert-warning">QR could not be generated. The link is: <code>{{ $scanUrl }}</code></div>
  @endif
</div>
@endsection
