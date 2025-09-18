@extends('public.layout')
@section('title','Volunteer Registration')
@section('content')
  @includeIf('auth.partials.register-form')
@endsection
