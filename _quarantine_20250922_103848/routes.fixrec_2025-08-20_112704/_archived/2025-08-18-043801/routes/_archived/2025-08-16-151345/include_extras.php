<?php
$extraFiles = [
    'pages.php',                // /
    'swaed_extras.php',         // certificate verify, misc
    'attendance.php',           // QR check in/out
    'public_events.php',        // /events  (named: public.events)
    'public_opportunities.php', // /opportunities (named: public.opportunities)
    'public_organizations.php', // /organizations (named: public.organizations)
    'public_gallery.php',       // /gallery (named: public.gallery)
    'seo.php',                  // /sitemap.xml
];
foreach ($extraFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) require $path;
}
