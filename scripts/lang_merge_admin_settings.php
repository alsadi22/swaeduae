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
  'admin_dashboard' => 'Admin Dashboard',
  'settings' => 'Settings',
  'back_to_dashboard' => 'Back to Dashboard',
  'users' => 'Users',
  'events' => 'Events',

  // Settings
  'uaepass' => 'UAE PASS',
  'social_login' => 'Social Login',
  'payment' => 'Payment',
  'save_settings' => 'Save Settings',

  // UAE PASS
  'uaepass_client_id' => 'UAE PASS Client ID',
  'uaepass_client_secret' => 'UAE PASS Client Secret',
  'uaepass_redirect_uri' => 'UAE PASS Redirect URI',

  // Social
  'google_client_id' => 'Google Client ID',
  'google_client_secret' => 'Google Client Secret',
  'facebook_app_id' => 'Facebook App ID',
  'facebook_app_secret' => 'Facebook App Secret',

  // Payment
  'payment_provider' => 'Payment Provider',
  'none' => 'None',
  'stripe_public_key' => 'Stripe Public Key',
  'stripe_secret_key' => 'Stripe Secret Key',
  'paytabs_profile_id' => 'PayTabs Profile ID',
  'paytabs_server_key' => 'PayTabs Server Key',
  'paytabs_region' => 'PayTabs Region',

  'settings_saved' => 'Settings saved successfully.',
];

$ar = [
  'admin_dashboard' => 'لوحة تحكم المدير',
  'settings' => 'الإعدادات',
  'back_to_dashboard' => 'العودة للوحة التحكم',
  'users' => 'المستخدمون',
  'events' => 'الفعاليات',

  // Settings
  'uaepass' => 'الهوية الرقمية (UAE PASS)',
  'social_login' => 'تسجيل الدخول الاجتماعي',
  'payment' => 'الدفع',
  'save_settings' => 'حفظ الإعدادات',

  // UAE PASS
  'uaepass_client_id' => 'معرّف عميل UAE PASS',
  'uaepass_client_secret' => 'سر عميل UAE PASS',
  'uaepass_redirect_uri' => 'رابط إعادة التوجيه لـ UAE PASS',

  // Social
  'google_client_id' => 'معرّف عميل Google',
  'google_client_secret' => 'سر عميل Google',
  'facebook_app_id' => 'معرّف تطبيق Facebook',
  'facebook_app_secret' => 'سر تطبيق Facebook',

  // Payment
  'payment_provider' => 'مزود الدفع',
  'none' => 'بدون',
  'stripe_public_key' => 'المفتاح العام لـ Stripe',
  'stripe_secret_key' => 'المفتاح السري لـ Stripe',
  'paytabs_profile_id' => 'معرّف ملف PayTabs',
  'paytabs_server_key' => 'مفتاح خادم PayTabs',
  'paytabs_region' => 'منطقة PayTabs',

  'settings_saved' => 'تم حفظ الإعدادات بنجاح.',
];

merge_lang(__DIR__ . '/../resources/lang/en/swaed.php', $en);
merge_lang(__DIR__ . '/../resources/lang/ar/swaed.php', $ar);
