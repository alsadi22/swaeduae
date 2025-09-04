@props([
    'type' => 'text',
    'name' => null,
    'value' => null,
    'disabled' => false,
    'autocomplete' => null,
    'placeholder' => null,
])
<input
    type="{{ $type }}"
    name="{{ $name }}"
    @if(!is_null($autocomplete)) autocomplete="{{ $autocomplete }}" @endif
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    value="{{ old($name, $value) }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => 'form-control']) }}
/>
