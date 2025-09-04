@props(['messages' => []])

@php
    $messages = is_array($messages) ? array_filter($messages) : (empty($messages) ? [] : [$messages]);
@endphp

@if ($messages)
    <div {{ $attributes->merge(['class' => 'text-danger small mt-1']) }}>
        @foreach ($messages as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
@endif
