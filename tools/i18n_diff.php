<?php
function load($dir){
  $all=[];
  if(!is_dir($dir)) return $all;
  foreach(glob("$dir/*.php") as $f){ $k=basename($f,'.php'); $all[$k]=include $f; }
  return new RecursiveIteratorIterator(new RecursiveArrayIterator($all));
}
function flatKeys($iter){
  $keys=[];
  $stack=[[$iter,'']];
  while($stack){
    [$it,$prefix]=array_pop($stack);
    foreach($it as $k=>$v){
      $key=$prefix.($prefix?'.':'').$k;
      if(is_array($v)) $stack[]= [new RecursiveArrayIterator($v), $key];
      else $keys[$key]=true;
    }
  }
  return $keys;
}
$en=flatKeys(load('resources/lang/en'));
$ar=flatKeys(load('resources/lang/ar'));
$missingInAr=array_diff_key($en,$ar);
$missingInEn=array_diff_key($ar,$en);
echo "Missing in ar:\n"; foreach(array_keys($missingInAr) as $k) echo "  - $k\n";
echo "\nMissing in en:\n"; foreach(array_keys($missingInEn) as $k) echo "  - $k\n";
