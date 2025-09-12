@extends('org.layout')
@section('title', __('Org Dashboard'))
@section('content')
  <h1 style="margin:0 0 12px">{{ __('Organization Dashboard') }}</h1>
  @php($org = $org ?? \DB::table('org_profiles')->where('user_id', auth()->id())->first())
  <p>{{ $org->org_name ?? $org->name ?? __('Welcome!') }}</p>

  <div style="margin-top:16px">
    <a class="btn" href="{{ url('/org/opportunities/create') }}">{{ __('Create Opportunity') }}</a>
    <a class="btn" href="{{ url('/org/settings') }}" style="margin-left:8px">{{ __('Settings') }}</a>
  </div>
@endsection
