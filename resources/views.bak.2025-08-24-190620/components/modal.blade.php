@props([
    'id',
    'title' => null,
    'static' => false,
])

@php
    $backdrop = $static ? 'static' : 'true';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="{{ $backdrop }}" data-bs-keyboard="{{ $static ? 'false' : 'true' }}">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      @if($title)
      <div class="modal-header">
        <h5 class="modal-title">{{ $title }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>
      @else
      <div class="modal-header border-0">
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>
      @endif

      <div class="modal-body">
        {{ $slot }}
      </div>

      @isset($footer)
      <div class="modal-footer">
        {{ $footer }}
      </div>
      @endisset
    </div>
  </div>
</div>
