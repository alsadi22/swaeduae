import React from "react";
const badges = [
  { name: "متطوع الشهر", icon: "🏅", desc: "إنجازات بارزة خلال الشهر." },
  { name: "سفير العمل التطوعي", icon: "🎖️", desc: "نشر روح التطوع عبر دعوة الأصدقاء." },
  { name: "محترف الساعات", icon: "⏰", desc: "إكمال أكثر من 100 ساعة تطوع." },
];
export default function Badges() {
  return (
    <div className="max-w-2xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">الأوسمة والمكافآت</h2>
      <div className="grid md:grid-cols-3 gap-8">
        {badges.map((b, i) => (
          <div key={i} className="bg-white shadow rounded-xl flex flex-col items-center p-8">
            <div className="text-5xl mb-3">{b.icon}</div>
            <div className="font-bold text-lg mb-1">{b.name}</div>
            <div className="text-gray-700 text-center">{b.desc}</div>
          </div>
        ))}
      </div>
    </div>
  );
}
