<?php
function merge($file,$arr){$cur=file_exists($file)?include $file:[]; if(!is_array($cur))$cur=[]; file_put_contents($file,"<?php\n\nreturn ".var_export($cur+$arr,true).";\n"); echo "Merged: $file\n";}
$en=[
  'application_submitted'=>'Application submitted',
  'my_applications'=>'My Applications',
  'no_applications'=>'No applications yet.',
  'login_required'=>'Please sign in to continue',
];
$ar=[
  'application_submitted'=>'تم إرسال طلبك',
  'my_applications'=>'طلباتي',
  'no_applications'=>'لا توجد طلبات بعد',
  'login_required'=>'يرجى تسجيل الدخول للمتابعة',
];
merge(__DIR__.'/../resources/lang/en/swaed.php',$en);
merge(__DIR__.'/../resources/lang/ar/swaed.php',$ar);
