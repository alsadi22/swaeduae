import React, { useState } from "react";
const events = [
  { id: 1, title: "حملة تنظيف الشواطئ", date: "2025-08-15", city: "دبي", spots: 50 },
  { id: 2, title: "زرع أشجار", date: "2025-08-18", city: "أبوظبي", spots: 30 },
  { id: 3, title: "مهرجان القراءة", date: "2025-08-25", city: "العين", spots: 80 },
];
export default function EventCalendar() {
  const [selected, setSelected] = useState(null);
  return (
    <div className="max-w-4xl mx-auto py-10 px-4">
      <h2 className="text-3xl font-bold mb-6">تقويم الفعاليات</h2>
      <div className="overflow-x-auto mb-6">
        <table className="w-full bg-white rounded-xl shadow text-right">
          <thead>
            <tr className="bg-blue-100">
              <th className="p-3 font-bold">التاريخ</th>
              <th className="p-3 font-bold">اسم الفعالية</th>
              <th className="p-3 font-bold">المدينة</th>
              <th className="p-3 font-bold">المقاعد المتاحة</th>
              <th className="p-3 font-bold"></th>
            </tr>
          </thead>
          <tbody>
            {events.map(e => (
              <tr key={e.id} className="border-t">
                <td className="p-3">{e.date}</td>
                <td className="p-3">{e.title}</td>
                <td className="p-3">{e.city}</td>
                <td className="p-3">{e.spots}</td>
                <td className="p-3">
                  <button onClick={() => setSelected(e)} className="bg-blue-600 text-white rounded px-4 py-1">تسجيل</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      {selected && (
        <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-lg p-8 text-right max-w-sm w-full relative">
            <button onClick={() => setSelected(null)} className="absolute left-4 top-4 text-gray-500">✕</button>
            <h3 className="font-bold text-xl mb-2">{selected.title}</h3>
            <div className="mb-2">التاريخ: {selected.date}</div>
            <div className="mb-2">المدينة: {selected.city}</div>
            <div className="mb-4">عدد المقاعد: {selected.spots}</div>
            <button className="w-full bg-green-600 text-white py-2 rounded font-bold">تأكيد التسجيل</button>
          </div>
        </div>
      )}
    </div>
  );
}
