<?php
function merge($file,$arr){$cur=file_exists($file)?include $file:[]; if(!is_array($cur))$cur=[]; file_put_contents($file,"<?php\n\nreturn ".var_export($cur+$arr,true).";\n"); echo "Merged: $file\n";}
$en=[
  'dashboard'=>'Dashboard','user_management'=>'User Management','name'=>'Name','role'=>'Role','status'=>'Status',
  'admin'=>'Admin','user'=>'User','active'=>'Active','inactive'=>'Inactive',
  'make_admin'=>'Make Admin','remove_admin'=>'Remove Admin','activate'=>'Activate','deactivate'=>'Deactivate',
  'all'=>'All','admins'=>'Admins','filter'=>'Filter','user_updated'=>'User updated.',
  'site'=>'Site','site_name'=>'Site Name','support_email'=>'Support Email'
];
$ar=[
  'dashboard'=>'لوحة التحكم','user_management'=>'إدارة المستخدمين','name'=>'الاسم','role'=>'الدور','status'=>'الحالة',
  'admin'=>'مدير','user'=>'مستخدم','active'=>'نشط','inactive'=>'غير نشط',
  'make_admin'=>'تعيين كمدير','remove_admin'=>'إزالة المدير','activate'=>'تفعيل','deactivate'=>'إيقاف',
  'all'=>'الكل','admins'=>'المدراء','filter'=>'تصفية','user_updated'=>'تم تحديث المستخدم.',
  'site'=>'الموقع','site_name'=>'اسم الموقع','support_email'=>'بريد الدعم'
];
merge(__DIR__.'/../resources/lang/en/swaed.php',$en);
merge(__DIR__.'/../resources/lang/ar/swaed.php',$ar);
