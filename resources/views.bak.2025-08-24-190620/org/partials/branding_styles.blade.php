

@if(!empty($color))
<style>
:root { --org-primary: {{ $color }}; }
.btn-primary, .bg-primary { background-color: var(--org-primary) !important; border-color: var(--org-primary) !important; }
a, .text-primary { color: var(--org-primary) !important; }
.form-check-input:checked { background-color: var(--org-primary) !important; border-color: var(--org-primary) !important; }
.badge.bg-primary { background-color: var(--org-primary) !important; }
</style>
@endif
