import React from "react";
export default function VolunteerLogbook() {
  // TODO: Replace with actual logged hours/events
  const log = [
    { event: "حملة التشجير", date: "2025-07-21", hours: 4 },
    { event: "ماراثون زايد", date: "2025-06-30", hours: 6 },
    { event: "توزيع وجبات", date: "2025-05-13", hours: 3 },
  ];
  return (
    <div className="max-w-3xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">سجل ساعات التطوع الخاص بك</h2>
      <table className="w-full bg-white rounded-xl shadow overflow-hidden">
        <thead>
          <tr className="bg-blue-50 text-blue-800 text-right">
            <th className="p-3 font-bold">الفعالية</th>
            <th className="p-3 font-bold">التاريخ</th>
            <th className="p-3 font-bold">عدد الساعات</th>
          </tr>
        </thead>
        <tbody>
          {log.map((item, i) => (
            <tr key={i} className="text-right border-t">
              <td className="p-3">{item.event}</td>
              <td className="p-3">{item.date}</td>
              <td className="p-3">{item.hours}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
