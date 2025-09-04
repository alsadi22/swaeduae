import React from "react";
export default function MyEvents() {
  // TODO: Connect to backend and get events
  const events = [
    { id: 1, title: "تنظيف الشاطئ", date: "2025-08-01", status: "تمت المشاركة" },
    { id: 2, title: "حملة التشجير", date: "2025-09-10", status: "مسجل" },
  ];
  return (
    <div className="max-w-3xl mx-auto py-10">
      <h2 className="text-2xl font-bold mb-6">فعالياتي</h2>
      <table className="w-full border rounded">
        <thead>
          <tr>
            <th className="border p-2">الفعالية</th>
            <th className="border p-2">التاريخ</th>
            <th className="border p-2">الحالة</th>
          </tr>
        </thead>
        <tbody>
          {events.map(ev => (
            <tr key={ev.id}>
              <td className="border p-2">{ev.title}</td>
              <td className="border p-2">{ev.date}</td>
              <td className="border p-2">{ev.status}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
