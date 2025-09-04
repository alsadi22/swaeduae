@extends('layouts.app')
@section('title', __('My Certificates').' | '.config('app.name'))

@section('content')
<div class="container py-3">
  <h1 class="h5 mb-3">{{ __('My Certificates') }}</h1>

  @if($rows->isEmpty())
    <div class="alert alert-light mb-0">{{ __('No certificates yet.') }}</div>
  @else
    <div class="table-responsive card p-3">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>{{ __('Code') }}</th>
            <th>{{ __('Issued') }}</th>
            <th class="text-end"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $c)
            <tr>
              <td class="fw-semibold">{{ $c->code }}</td>
              <td>{{ \Carbon\Carbon::parse($c->created_at)->format('Y-m-d H:i') }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('certificates.show', $c->code) }}">
                  {{ __('Open') }}
                </a>
                <a class="btn btn-sm btn-light" href="{{ route('certificates.verify', $c->code) }}">
                  {{ __('Verify') }}
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $rows->links() }}</div>
  @endif
</div>
@endsection
