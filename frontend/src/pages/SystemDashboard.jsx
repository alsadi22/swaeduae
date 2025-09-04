import React from "react";
export default function SystemDashboard() {
  // TODO: Replace with live stats from backend
  const stats = [
    { label: "عدد المتطوعين", value: 4302 },
    { label: "الفعاليات النشطة", value: 16 },
    { label: "ساعات التطوع المنجزة", value: 21450 },
    { label: "عدد الشهادات المصدرة", value: 3872 },
    { label: "المؤسسات المشاركة", value: 24 },
  ];
  return (
    <div className="max-w-5xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-8">لوحة تحكم النظام</h2>
      <div className="grid md:grid-cols-3 gap-6">
        {stats.map((s, i) => (
          <div key={i} className="bg-gradient-to-tr from-blue-600 to-green-400 text-white rounded-xl shadow-xl p-8 text-center text-2xl font-bold flex flex-col items-center">
            <span className="text-4xl mb-2">{s.value.toLocaleString()}</span>
            <span className="text-lg">{s.label}</span>
          </div>
        ))}
      </div>
    </div>
  );
}
