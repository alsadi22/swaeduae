<!doctype html>
<html lang="{{ str_replace("_","-",app()->getLocale()) }}" dir="{{ app()->getLocale()==="ar" ? "rtl" : "ltr" }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield("title","SwaedUAE")</title>

  <!-- Fonts (display=swap) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- Theme CSS -->
  <link href="/assets/app.css"assets/app.css")) ? substr(md5_file(public_path("assets/app.css")),0,8) : time() }}" rel="stylesheet">
  <link rel="stylesheet" href="/assets/app.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="public-site">
  <a class="skip-link" href="#main">Skip to content</a>
  @includeIf("partials.header")

  <main id="main">@yield("content")</main>

  @includeIf("partials.footer")

  <!-- Minimal JS (a11y + mobile nav + persist) -->
  <script src="/assets/app.js?v={{ file_exists(public_path("assets/app.js")) ? substr(md5_file(public_path("assets/app.js")),0,8) : time() }}"></script>
  <!-- nav-dropdown-toggle-fix: inserted 20250914-201151 -->
  <script id="nav-dropdown-toggle-fix">
  // Toggle Sign In / Sign Up dropdown when chevron is clicked (text still navigates)
  document.addEventListener("click", function(ev){
    const a = ev.target.closest("a");
    if(!a) return;
    const label = (a.textContent||"").trim();
    if(label!=="Sign In" && label!=="Sign Up") return;
    const onChevron = ev.target.closest("[data-feather=\"chevron-down\"]");
    if(!onChevron) return; // click on text navigates as usual
    ev.preventDefault();
    const group = a.closest(".group") || a.parentElement;
    if(!group) return;
    const menu = group.querySelector("div.absolute");
    if(!menu) return;
    menu.classList.toggle("opacity-0");
    menu.classList.toggle("invisible");
    // close on outside click
    const closer = (e2)=>{
      if(!menu.contains(e2.target) && !a.contains(e2.target)){
        menu.classList.add("opacity-0","invisible");
        document.removeEventListener("click", closer, true);
      }
    };
    document.addEventListener("click", closer, true);
  }, true);
  </script>
    <script src="/assets/nav-dropdown-fix.js"></script>
</body>
    <script src="/assets/feather.min.js"></script>
    <script>document.addEventListener("DOMContentLoaded",function(){ if (window.feather && feather.replace) feather.replace(); });</script>
</html>
