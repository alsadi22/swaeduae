import React from "react";
export default function AppDownloadBar() {
  return (
    <div className="flex justify-center gap-4 mt-8 mb-2">
      <a href="#" className="flex items-center gap-2 border rounded-xl px-4 py-2 bg-white shadow hover:bg-gray-50">
        <img src="/img/appstore.svg" alt="Apple" className="h-7" /> <span>حمل الملف على متجر التطبيقات</span>
      </a>
      <a href="#" className="flex items-center gap-2 border rounded-xl px-4 py-2 bg-white shadow hover:bg-gray-50">
        <img src="/img/playstore.svg" alt="Google" className="h-7" /> <span>احصل عليه على جوجل بلاي</span>
      </a>
    </div>
  );
}
