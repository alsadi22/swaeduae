@extends('layouts.app')
@section('title', $name)
@section('content')
<div class="container py-4">
  <a class="btn btn-sm btn-outline-secondary mb-3" href="{{ route('orgs.public.index') }}">← {{ __('All organizations') }}</a>
  <div class="card p-3 mb-3">
    <h2 class="mb-1">{{ $name }}</h2>
    @php
      $about = '';
      foreach (['about','description'] as $col) { if(!empty($o->$col)){ $about=$o->$col; break; } }
      $emirate = $o->emirate ?? ($o->region ?? ($o->city ?? null));
    @endphp
    @if($emirate)<div class="text-muted mb-2">{{ $emirate }}</div>@endif
    @if($about)<div class="mb-2">{!! nl2br(e($about)) !!}</div>@endif
  </div>

  <h4 class="mb-2">{{ __('Latest Opportunities') }}</h4>
  @if(($opps->count() ?? 0) === 0)
    <div class="alert alert-info">{{ __('No recent opportunities from this organization.') }}</div>
  @else
    <div class="list-group">
      @foreach($opps as $p)
        <a class="list-group-item list-group-item-action" href="{{ route('public.opportunity.show', ['id'=>$p->id]) }}">
          {{ $p->title ?? __('Opportunity') }} <span class="text-muted">#{{ $p->id }}</span>
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection
