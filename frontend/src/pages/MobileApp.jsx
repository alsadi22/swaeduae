import React from "react";
export default function MobileApp() {
  return (
    <div className="max-w-xl mx-auto py-10 px-4 text-center">
      <h2 className="text-2xl font-bold mb-4">حمّل تطبيق سواعد الإمارات</h2>
      <p className="mb-4">تجربة متكاملة عبر الجوال! تصفح الفعاليات، سجل حضورك، وحصل على شهاداتك فوراً.</p>
      <div className="flex items-center justify-center gap-6 mb-8">
        <a href="https://apps.apple.com" target="_blank"><img alt="App Store" className="w-32" src="/apple-store-badge.svg"/></a>
        <a href="https://play.google.com" target="_blank"><img alt="Google Play" className="w-32" src="/google-play-badge.png"/></a>
      </div>
      <div className="bg-blue-100 p-4 rounded-xl">
        <div className="font-bold mb-2">تثبيت كتطبيق ويب:</div>
        <ol className="list-decimal text-right mr-6 text-sm text-gray-700">
          <li>افتح الموقع من الجوال.</li>
          <li>اضغط <b>إضافة إلى الشاشة الرئيسية</b> من القائمة.</li>
        </ol>
      </div>
    </div>
  );
}
