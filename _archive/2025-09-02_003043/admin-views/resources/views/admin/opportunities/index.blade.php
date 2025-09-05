@extends('admin.layout')
@section('title', __('Opportunities'))
@section('page_title', __('Opportunities'))
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow border-0">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ __('Opportunities') }}</h6>
        <a href="{{ route('admin.opportunities.create') }}" class="btn btn-sm btn-primary">{{ __('New Opportunity') }}</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Title') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Org') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Start') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse(($opportunities ?? []) as $op)
                <tr>
                  <td class="text-sm">{{ $op->id }}</td>
                  <td class="text-sm">{{ $op->title }}</td>
                  <td class="text-sm">{{ $op->organization->name ?? '—' }}</td>
                  <td class="text-sm">{{ optional($op->start_date)->format('Y-m-d') ?? '—' }}</td>
                  <td class="text-end">
                    <a href="{{ route('admin.opportunities.edit',$op->id) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-secondary p-4">— {{ __('No opportunities yet') }} —</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if(method_exists(($opportunities ?? null), 'links'))
        <div class="card-footer d-flex justify-content-end">
          {{ $opportunities->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
