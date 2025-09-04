<?php
function loadPack($dir){
  $all=[];
  if(!is_dir($dir)) return $all;
  foreach(glob("$dir/*.php") as $f){ $k=basename($f,'.php'); $all[$k]=include $f; }
  return $all;
}
function flatten($arr,$prefix=''){
  $out=[];
  foreach($arr as $k=>$v){
    $key=$prefix? "$prefix.$k" : $k;
    if(is_array($v)) $out+=flatten($v,$key);
    else $out[$key]=$v;
  }
  return $out;
}
$en=flatten(loadPack('resources/lang/en'));
$ar=flatten(loadPack('resources/lang/ar'));
$keys=array_unique(array_merge(array_keys($en),array_keys($ar)));
$fp=fopen('storage/i18n_gaps.csv','w');
fputcsv($fp,['key','en','ar']);
foreach($keys as $k){ fputcsv($fp, [$k, $en[$k]??'', $ar[$k]??'']); }
fclose($fp);
echo "[OK] storage/i18n_gaps.csv\n";
