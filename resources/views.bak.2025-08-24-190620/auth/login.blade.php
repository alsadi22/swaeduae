@extends('layouts.app')
@section('content')
  @include('auth.partials.login-form')
@endsection

@includeIf('auth._social_logins')
