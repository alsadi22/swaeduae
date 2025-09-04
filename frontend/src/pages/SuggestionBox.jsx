import React from "react";
export default function SuggestionBox() {
  return (
    <div className="max-w-lg mx-auto py-12 px-6">
      <h2 className="text-2xl font-bold mb-6">صندوق الاقتراحات</h2>
      <form className="bg-white rounded-xl shadow p-6 flex flex-col gap-4">
        <input className="p-2 border rounded" placeholder="اسمك (اختياري)" />
        <textarea className="p-2 border rounded" rows={5} placeholder="اكتب اقتراحك أو بلاغك هنا"></textarea>
        <button className="bg-blue-600 text-white py-2 rounded">إرسال</button>
      </form>
    </div>
  );
}
