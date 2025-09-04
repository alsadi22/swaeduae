<?php
$index='https://swaeduae.ae/sitemaps/sitemap-index.xml';
$xml=@simplexml_load_string(file_get_contents($index));
if(!$xml){ fwrite(STDERR,"[ERR] cannot load index\n"); exit(1); }
$urls=[];
foreach($xml->sitemap as $s){
  $sm=(string)$s->loc;
  $sx=@simplexml_load_string(file_get_contents($sm));
  if(!$sx) continue;
  foreach($sx->url as $u){ $urls[]=(string)$u->loc; }
}
$urls=array_slice($urls,0,100); // keep it light
printf("%-5s %-60s %-4s %-7s %s\n","IDX","URL","Code","Title?","Desc?");
$idx=0;
foreach($urls as $u){
  $h=@get_headers($u,1);
  $code=0;
  if($h && preg_match('#\s(\d{3})\s#',$h[0],$m)) $code=(int)$m[1];
  $html=@file_get_contents($u);
  $hasTitle = (bool)preg_match('/<title>\s*[^<]+<\/title>/i',$html);
  $hasDesc  = (bool)preg_match('/<meta\s+name=["\']description["\']\s+content=["\'][^"\']+["\']/i',$html);
  printf("%-5d %-60s %-4d %-7s %s\n", ++$idx, $u, $code, $hasTitle?'yes':'no', $hasDesc?'yes':'no');
}
