<?php
$roots = ['resources','app','routes','config'];
$patterns = [
  "/route\\(\\s*['\"]about['\"]\\s*\\)/i"   => "route('pages.about')",
  "/route\\(\\s*['\"]privacy['\"]\\s*\\)/i" => "route('pages.privacy')",
  "/route\\(\\s*['\"]terms['\"]\\s*\\)/i"   => "route('pages.terms')",
  "/route\\(\\s*['\"]contact['\"]\\s*\\)/i" => "route('contact.show')", // /contact-us
];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__."/..", FilesystemIterator::SKIP_DOTS));
$sc=0;$pt=0;
foreach ($rii as $f) {
  $p=$f->getPathname();
  if (!preg_match('~/(' . implode('|',$roots) . ')/~',$p)) continue;
  if (!preg_match('/\.php$/',$p)) continue;  // covers *.php and *.blade.php
  $sc++;
  $old=file_get_contents($p); $new=$old;
  foreach ($patterns as $pat=>$to) $new=preg_replace($pat,$to,$new);
  if ($new!==$old) { file_put_contents($p,$new); echo "patched: $p\n"; $pt++; }
}
echo "scanned=$sc patched=$pt\n";
