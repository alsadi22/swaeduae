@extends(public.layout)
#@section('title','Organization Registration')
#@section('content')
  @includeIf('org.auth._register_form_basic')
#endsection
