@props(['id', 'title' => null, 'static' => false])

<x-modal :id="$id" :title="$title" :static="$static">
    {{ $slot }}
    @isset($footer)
        <x-slot name="footer">
            {{ $footer }}
        </x-slot>
    @endisset
</x-modal>
