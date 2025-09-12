@extends('admin.layout')

@section('admin-content')
<div class="py-3">
  <h1 class="mb-3">{{ $title ?? __('Admin') }}</h1>
  <p class="text-muted">{{ __('This section is ready. Connect the controller or view to replace this placeholder.') }}</p>
</div>
@endsection
