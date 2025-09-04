import React from "react";
export default function AdminCMS() {
  // TODO: Connect to real CMS backend for static pages
  const pages = [
    { name: "حول المنصة", path: "/about" },
    { name: "الأسئلة الشائعة", path: "/faq" },
    { name: "سياسة الخصوصية", path: "/privacy" },
    { name: "سياسة الوصول الإلكتروني", path: "/accessibility" },
  ];
  return (
    <div className="max-w-4xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">إدارة محتوى الصفحات الثابتة</h2>
      <div className="bg-white rounded-xl shadow p-6">
        <table className="w-full text-right">
          <thead>
            <tr className="bg-gray-50 text-gray-700">
              <th className="p-2">اسم الصفحة</th>
              <th className="p-2">الرابط</th>
              <th className="p-2">تحرير</th>
            </tr>
          </thead>
          <tbody>
            {pages.map((p, i) => (
              <tr key={i} className="border-t">
                <td className="p-2">{p.name}</td>
                <td className="p-2 text-blue-600">{p.path}</td>
                <td className="p-2"><button className="text-green-600 underline">تعديل</button></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
