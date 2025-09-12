import React from "react";
export default function Partners() {
  return (
    <div className="max-w-4xl mx-auto py-12 px-6">
      <h2 className="text-2xl font-bold mb-6">شركاء النجاح</h2>
      <div className="flex flex-wrap gap-8 items-center justify-center">
        {["الهلال الأحمر", "بلدية دبي", "شرطة أبوظبي", "مؤسسة زايد"].map((p, i) => (
          <div key={i} className="bg-white shadow rounded-xl p-6 text-xl font-bold text-blue-700">{p}</div>
        ))}
      </div>
    </div>
  );
}
