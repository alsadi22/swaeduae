<?php
function merge($file,$arr){$cur=file_exists($file)?include $file:[]; if(!is_array($cur))$cur=[]; file_put_contents($file,"<?php\n\nreturn ".var_export($cur+$arr,true).";\n"); echo "Merged: $file\n";}
$en=[
  'applicants'=>'Applicants','approve'=>'Approve','reject'=>'Reject','approved'=>'Approved','rejected'=>'Rejected',
  'back'=>'Back','applied_at'=>'Applied','actions'=>'Actions','name'=>'Name','email'=>'Email'
];
$ar=[
  'applicants'=>'المتقدّمون','approve'=>'قبول','reject'=>'رفض','approved'=>'تم القبول','rejected'=>'تم الرفض',
  'back'=>'رجوع','applied_at'=>'تاريخ التقديم','actions'=>'الإجراءات','name'=>'الاسم','email'=>'البريد الإلكتروني'
];
merge(__DIR__.'/../resources/lang/en/swaed.php',$en);
merge(__DIR__.'/../resources/lang/ar/swaed.php',$ar);
