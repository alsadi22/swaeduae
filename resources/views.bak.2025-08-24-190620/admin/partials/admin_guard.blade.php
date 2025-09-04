@php
    $u = auth()->user();
    if (!$u || !($u->is_admin ?? false) && !(method_exists($u, 'hasRole') && $u->hasRole('admin'))) {
        abort(403);
    }
@endphp
