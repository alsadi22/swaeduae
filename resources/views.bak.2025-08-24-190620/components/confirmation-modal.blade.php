@props([
    'id',
    'title' => __('Confirm action'),
    'message' => __('Are you sure you want to proceed?'),
])

<x-modal :id="$id" :title="$title">
    <p class="mb-0">{{ $message }}</p>

    <x-slot name="footer">
        {{ $footer ?? '' }}
    </x-slot>
</x-modal>
