@extends('layouts.admin')
@section('title','Opportunity')
@section('content')
  <h1 class="mb-3">Opportunity</h1>
  <pre>{{ json_encode($opportunity ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
@endsection
