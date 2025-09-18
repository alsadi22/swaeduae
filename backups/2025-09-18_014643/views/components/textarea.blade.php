@props([
    'name' => null,
    'rows' => 3,
    'placeholder' => null,
])
<textarea
    name="{{ $name }}"
    rows="{{ $rows }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    {{ $attributes->merge(['class' => 'form-control']) }}
>{{ old($name, $slot) }}</textarea>
