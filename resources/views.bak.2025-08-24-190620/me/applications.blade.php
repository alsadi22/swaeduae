@extends('layouts.app')
@section('title', __('My Applications').' | '.config('app.name'))

@section('content')
<div class="container py-3">
  <h1 class="h4 mb-3">{{ __('My Applications') }}</h1>

  @if($rows->isEmpty())
    <div class="alert alert-light mb-0">{{ __('You have not applied to any opportunities yet.') }}</div>
  @else
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>{{ __('Opportunity') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Applied at') }}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $r)
            <tr>
              <td>
                <a href="{{ route('public.opportunities.show', $r->opportunity_id) }}">{{ $r->title }}</a>
              </td>
              <td><span class="badge bg-secondary text-capitalize">{{ $r->status }}</span></td>
              <td>{{ \Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</td>
              <td class="text-end">
                <form method="POST"
                      action="{{ route('opportunities.withdraw', $r->opportunity_id) }}"
                      onsubmit="return confirm('{{ __('Withdraw application?') }}')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">{{ __('Withdraw') }}</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{ $rows->links() }}
  @endif
</div>
@endsection
