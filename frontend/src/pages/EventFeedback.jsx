import React from "react";
export default function EventFeedback() {
  return (
    <div className="max-w-lg mx-auto py-12 px-6">
      <h2 className="text-2xl font-bold mb-6">تقييم الفعالية</h2>
      <form className="bg-white rounded-xl shadow p-6 flex flex-col gap-4">
        <label>كيف تقيم تجربتك؟</label>
        <select className="p-2 border rounded">
          <option>ممتاز</option>
          <option>جيد جداً</option>
          <option>جيد</option>
          <option>مقبول</option>
        </select>
        <textarea className="p-2 border rounded" rows={5} placeholder="ملاحظاتك"></textarea>
        <button className="bg-green-600 text-white py-2 rounded">إرسال التقييم</button>
      </form>
    </div>
  );
}
