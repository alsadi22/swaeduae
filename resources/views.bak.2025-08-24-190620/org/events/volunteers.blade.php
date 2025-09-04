@extends('org.layout')

@section('title', __('Event Volunteers'))

@section('content')
<div class="container-fluid mt-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">
            {{ __('Event Volunteers') }}
            @if(!empty($opportunity->title)) — <small class="text-muted">{{ $opportunity->title }}</small>@endif
        </h1>
        <a class="btn btn-primary" href="{{ route('org.events.volunteers.csv', $opportunityId) }}">{{ __('Download CSV') }}</a>
    </div>

    {{-- flash banners --}}
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('org.events._volunteers_table', ['rows' => ($rows ?? []), 'opportunityId' => $opportunityId])
</div>
@endsection
