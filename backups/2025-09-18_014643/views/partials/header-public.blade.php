
<!-- Hard-link a[data-nav] even if other JS cancels the click (capture phase) -->
<script id="auth-nav-capture">
document.addEventListener('click', function(e){
  var a = e.target && e.target.closest ? e.target.closest('a[data-nav]') : null;
  if (!a) return;
  // Navigate immediately; capture-phase runs before any preventDefault on bubble/target
  try { window.location.assign(a.getAttribute('href')); } catch(_) {}
}, true); // <-- CAPTURE PHASE
</script>
