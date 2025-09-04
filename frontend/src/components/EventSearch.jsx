import React from "react";
export default function EventSearch() {
  return (
    <section className="w-full max-w-4xl mx-auto mb-8 p-4 bg-white/80 rounded-xl shadow-lg -mt-10 z-10 relative">
      <form className="flex flex-col md:flex-row gap-3 items-center">
        <select className="border rounded px-3 py-2 w-full md:w-auto">
          <option>المكان</option>
          <option>أبوظبي</option>
          <option>دبي</option>
          <option>الشارقة</option>
          <option>عجمان</option>
          <option>رأس الخيمة</option>
        </select>
        <select className="border rounded px-3 py-2 w-full md:w-auto">
          <option>المؤسسة</option>
          <option>الهلال الأحمر</option>
          <option>وزارة تنمية المجتمع</option>
          <option>مؤسسة الإمارات</option>
        </select>
        <select className="border rounded px-3 py-2 w-full md:w-auto">
          <option>الفئة</option>
          <option>خدمة المجتمع</option>
          <option>الرياضة</option>
          <option>صحة</option>
          <option>تكنولوجيا</option>
        </select>
        <button type="submit" className="bg-orange-500 hover:bg-orange-600 transition text-white rounded px-6 py-2 font-bold">
          تصفح الفرص التطوعية
        </button>
      </form>
    </section>
  );
}
