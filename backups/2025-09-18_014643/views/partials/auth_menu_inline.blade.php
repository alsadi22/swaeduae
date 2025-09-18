<div class="flex items-center gap-3">
  <div class="relative dropdown">
    <a class="link-secondary dropdown-toggle" href="#" id="authMenu" data-bs-toggle="dropdown" aria-expanded="false">Sign In</a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authMenu">
      <li><a class="dropdown-item" role="menuitem" data-nav="1" href="/login">Volunteer Sign In</a></li>
      <li><a class="dropdown-item" role="menuitem" data-nav="1" href="/org/login">Organization Sign In</a></li>
    </ul>
  </div>
  <div class="relative dropdown">
    <a class="btn btn-outline-primary dropdown-toggle" href="#" id="authMenuUp" data-bs-toggle="dropdown" aria-expanded="false">Sign Up</a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authMenuUp">
      <li><a class="dropdown-item" role="menuitem" data-nav="1" href="/register">Volunteer Sign Up</a></li>
      <li><a class="dropdown-item" role="menuitem" data-nav="1" href="/org/register">Organization Sign Up</a></li>
    </ul>
  </div>
</div>

<script id="global-auth-nav-delegate-inline">
document.addEventListener('click', function(e){
  var a = e.target && e.target.closest ? e.target.closest('a[data-nav]') : null;
  if(a){ window.location.assign(a.getAttribute('href')); }
});
</script>
