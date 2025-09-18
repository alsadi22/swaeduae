<div class="dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="authMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    Sign In
  </a>
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authMenu">
    <li><a class="dropdown-item" role="menuitem" data-nav="1" href="/login">Volunteer Sign In</a></li>
    <li><a class="dropdown-item" role="menuitem" data-nav="1" href="/org/login">Organization Sign In</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item" role="menuitem" data-nav="1" href="/register">Volunteer Sign Up</a></li>
    <li><a class="dropdown-item" role="menuitem" data-nav="1" href="/org/register">Organization Sign Up</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item" href="/admin/login">Admin</a></li>
  </ul>
</div>

<script id="global-auth-nav-delegate">
document.addEventListener('click', function(e){
  var a = e.target && e.target.closest ? e.target.closest('a[data-nav]') : null;
  if(a){ window.location.assign(a.getAttribute('href')); }
});
</script>
