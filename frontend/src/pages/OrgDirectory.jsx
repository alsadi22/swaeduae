import React from "react";
export default function OrgDirectory() {
  // TODO: Replace with real API data
  const orgs = [
    { name: "الهلال الأحمر", desc: "جمعية إنسانية وطنية", logo: "/org1.png", city: "أبوظبي" },
    { name: "مؤسسة زايد", desc: "مبادرات إنسانية وتنموية", logo: "/org2.png", city: "دبي" },
    { name: "شرطة الشارقة", desc: "برامج الأمن المجتمعي", logo: "/org3.png", city: "الشارقة" },
  ];
  return (
    <div className="max-w-5xl mx-auto py-10 px-4">
      <h2 className="text-3xl font-bold mb-8">دليل الجهات والمؤسسات</h2>
      <div className="grid md:grid-cols-3 gap-8">
        {orgs.map((org, i) => (
          <div key={i} className="bg-white shadow rounded-xl p-6 flex flex-col items-center">
            <img src={org.logo} alt={org.name} className="w-20 h-20 rounded-full mb-4" />
            <div className="font-bold text-lg mb-1">{org.name}</div>
            <div className="text-sm text-gray-600 mb-2">{org.city}</div>
            <div className="text-gray-700 text-center">{org.desc}</div>
          </div>
        ))}
      </div>
    </div>
  );
}
