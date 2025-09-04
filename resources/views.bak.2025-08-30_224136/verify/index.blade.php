@extends('layouts.app')
@section('title', __('Verify Certificate'))
@section('content')
<div class="container py-4">
  <h1 class="mb-3" style="font-weight:700;">{{ __('Verify Certificate') }} / تحقق من الشهادة</h1>
  <form method="GET" action="{{ url('/verify') }}" class="row g-2 mb-3">
    <div class="col-md-6"><input class="form-control" name="code" placeholder="SU-XXXX-YYMMDD" value="{{ request('code') }}"></div>
    <div class="col-md-3"><button class="btn btn-primary w-100" style="background:#9CAFAA;border-color:#9CAFAA;">{{ __('Verify') }}</button></div>
  </form>
  @if(request('code'))
    <script>location.href = "{{ url('/verify/'.urlencode(request('code'))) }}";</script>
  @endif
</div>
@endsection
