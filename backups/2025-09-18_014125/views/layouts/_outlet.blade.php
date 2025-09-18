@php($__slot = $slot ?? null)
@if($__slot)
  {{ $__slot }}
@elseif(View::hasSection('page'))
  @yield('page')
@elseif(View::hasSection('content'))
  @yield('content')
@endif
