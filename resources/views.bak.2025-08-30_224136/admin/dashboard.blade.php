@extends('admin.layouts.mini')

@section('page_title','Dashboard')

@section('content')
  <h1 style="margin:0 0 12px 0">Welcome</h1>
  <p>You are signed in as <strong>{{ optional(auth('admin')->user())->email }}</strong>.</p>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:16px">
    @php $links = [
      ['admin.users.index','Users'],
      ['admin.organizations.index','Organizations'],
      ['admin.opportunities.index','Opportunities'],
      ['admin.events.index','Events'],
      ['admin.certificates.index','Certificates'],
      ['admin.settings.index','Settings'],
    ]; @endphp

    @foreach($links as [$route,$label])
      @if(\Illuminate\Support\Facades\Route::has($route))
        <a href="{{ route($route) }}"
           style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;
                  padding:14px;text-decoration:none;color:#111827">{{ $label }}</a>
      @endif
    @endforeach
  </div>
@endsection
