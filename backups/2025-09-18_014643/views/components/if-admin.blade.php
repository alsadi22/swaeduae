@php($u = auth()->user())
@if($u && (
       (method_exists($u,'hasRole') && $u->hasRole('admin')) ||
       (method_exists($u,'can') && $u->can('access admin')) ||
       (isset($u->is_admin) && $u->is_admin)
    ))
    {{ $slot }}
@endif
