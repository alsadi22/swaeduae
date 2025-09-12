<?php
function merge_lang($file, $new) {
    $arr = [];
    if (file_exists($file)) {
        $arr = include $file;
        if (!is_array($arr)) $arr = [];
    }
    $merged = $arr + $new;
    $code = "<?php\n\nreturn " . var_export($merged, true) . ";\n";
    file_put_contents($file, $code);
    echo "Merged: $file\n";
}

$en = [
  'attendance_for' => 'Attendance for',
  'view_event' => 'View Event',
  'application_status' => 'Application Status',
  'hours' => 'Hours',
  'check_in' => 'Check-in',
  'check_out' => 'Check-out',
  'notes' => 'Notes',
  'optional' => 'Optional',
  'update' => 'Update',
  'hours_updated' => 'Hours updated.',
  'my_hours' => 'My Hours',
  'total_hours' => 'Total Hours',
  'no_hours_yet' => 'No hours recorded yet.',
  'certificates' => 'Certificates',
  'issue_certificate' => 'Issue Certificate',
  'user_email' => 'User Email',
  'event_id' => 'Event ID',
  'auto' => 'Auto',
  'issue' => 'Issue',
  'back' => 'Back',
  'view' => 'View',
  'no_certificates' => 'No certificates found.',
  'code' => 'Code',
  'user_not_found' => 'User not found.',
  'code_exists' => 'This code already exists.',
  'certificate_issued' => 'Certificate issued:',
];

$ar = [
  'attendance_for' => 'الحضور لـ',
  'view_event' => 'عرض الفعالية',
  'application_status' => 'حالة الطلب',
  'hours' => 'الساعات',
  'check_in' => 'تسجيل الدخول',
  'check_out' => 'تسجيل الخروج',
  'notes' => 'ملاحظات',
  'optional' => 'اختياري',
  'update' => 'تحديث',
  'hours_updated' => 'تم تحديث الساعات.',
  'my_hours' => 'ساعاتي',
  'total_hours' => 'إجمالي الساعات',
  'no_hours_yet' => 'لا توجد ساعات مسجلة بعد.',
  'certificates' => 'الشهادات',
  'issue_certificate' => 'إصدار شهادة',
  'user_email' => 'البريد الإلكتروني للمستخدم',
  'event_id' => 'رقم الفعالية',
  'auto' => 'تلقائي',
  'issue' => 'إصدار',
  'back' => 'رجوع',
  'view' => 'عرض',
  'no_certificates' => 'لا توجد شهادات.',
  'code' => 'الرمز',
  'user_not_found' => 'المستخدم غير موجود.',
  'code_exists' => 'هذا الرمز موجود مسبقاً.',
  'certificate_issued' => 'تم إصدار الشهادة:',
];

merge_lang(__DIR__ . '/../resources/lang/en/swaed.php', $en);
merge_lang(__DIR__ . '/../resources/lang/ar/swaed.php', $ar);
