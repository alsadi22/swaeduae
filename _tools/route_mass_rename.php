<?php
$base = __DIR__ . '/../resources/views';
$it = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
);

// 1) rename-only mapping (keeps the rest of the arguments)
//    route('old', <args>...)  => route('new', <same args>...)
$nameMap = [
  // language switch
  'locale.switch'                => 'lang.switch',

  // public collections -> real ones
  'public.events'                => 'events.index',
  'public.events.show'           => 'events.show',
  'public.opportunities'         => 'opportunities.index',
  'public.opportunities.show'    => 'opps.public.show',
  'public.opportunity.show'      => 'opps.public.show',

  // theme-demo leftovers -> your pages
  'index'                        => 'home',
  'index2'                       => 'home',
  'index3'                       => 'home',
  'destination'                  => 'events.index',
  'destinationdetails'           => 'events.index',
  'tour'                         => 'events.index',
  'tourdetails'                  => 'events.index',
  'blog'                         => 'events.index',
  'blogdetails'                  => 'events.index',

  // org naming consistency
  'organization.opportunities.create' => 'org.opportunities.create',
  'org.opps.index'               => 'org.opportunities.index',
  'org.opps.create'              => 'org.opportunities.create',
  'org.opps.store'               => 'org.opportunities.store',
  'org.opps.edit'                => 'org.opportunities.edit',
  'org.opps.update'              => 'org.opportunities.update',
  'org.opps.destroy'             => 'org.opportunities.destroy',

  // misc
  'qr.verify'                    => 'scan.index',
  'login.organization'           => 'org.login',
];

// 2) full replacement to URL (these POST routes are unnamed in Laravel)
$toUrlMap = [
  'login.perform'                => '/login',
  'register.perform'             => '/register',
  'public.organizations'         => '/partners',
  'public.gallery'               => '/',
  'register.organization.store'  => '/org/register',  // if present anywhere
  'organization.register.store'  => '/org/register',
  'organizations.register.store' => '/org/register',
];

// helper: replace only the route NAME token, preserve args
function replaceNameTokens($content, $nameMap) {
  return preg_replace_callback(
    "/route\\(\\s*(['\"])([a-zA-Z0-9._-]+)\\1/i",
    function($m) use ($nameMap){
      $q = $m[1]; $old = $m[2];
      if (!isset($nameMap[$old])) return $m[0];
      $new = $nameMap[$old];
      return "route(".$q.$new.$q; // rest of args remain intact
    },
    $content
  );
}

$patched = 0;
foreach ($it as $f) {
  $p = $f->getPathname();
  if (!preg_match('/\\.blade\\.php$/', $p)) continue;

  $old = file_get_contents($p);
  $new = $old;

  // rename route names (keep args)
  $new = replaceNameTokens($new, $nameMap);

  // convert some route(...) calls to url('/...') entirely
  foreach ($toUrlMap as $bad => $url) {
    $new = preg_replace(
      "/route\\(\\s*['\"]".$bad."['\"][^)]*\\)/i",
      "url('".$url."')",
      $new
    );
  }

  if ($new !== $old) {
    file_put_contents($p, $new);
    echo "patched: $p\n";
    $patched++;
  }
}
echo "done, patched=$patched\n";
