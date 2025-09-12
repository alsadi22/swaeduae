import React from "react";
const leaders = [
  { name: "أحمد السويدي", hours: 134, img: "/avatars/ahmed.jpg" },
  { name: "مريم الحوسني", hours: 120, img: "/avatars/maryam.jpg" },
  { name: "سعيد المهيري", hours: 98, img: "/avatars/saeed.jpg" },
];
export default function Leaderboard() {
  return (
    <div className="max-w-2xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">قائمة المتصدرين</h2>
      <div className="bg-white rounded-xl shadow">
        <table className="w-full text-right">
          <thead>
            <tr className="bg-blue-100">
              <th className="p-3 font-bold">#</th>
              <th className="p-3 font-bold">الاسم</th>
              <th className="p-3 font-bold">الساعات</th>
            </tr>
          </thead>
          <tbody>
            {leaders.map((l,i)=>(
              <tr key={i} className="border-t">
                <td className="p-3">{i+1}</td>
                <td className="p-3 flex items-center gap-2">
                  <img src={l.img} className="w-8 h-8 rounded-full inline-block" alt={l.name}/> {l.name}
                </td>
                <td className="p-3">{l.hours}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
