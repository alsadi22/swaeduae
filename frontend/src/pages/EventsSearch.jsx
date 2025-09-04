import React from "react";
import { Link } from "react-router-dom";
export default function EventsSearch() {
  // TODO: implement API search/filter
  return (
    <div className="max-w-6xl mx-auto py-10 px-4">
      <h2 className="text-3xl font-bold mb-6">استعرض الفعاليات التطوعية</h2>
      <form className="flex flex-wrap gap-4 mb-8">
        <input className="p-2 rounded border w-40" placeholder="ابحث باسم الفعالية..." />
        <select className="p-2 rounded border w-40"><option>كل المدن</option></select>
        <select className="p-2 rounded border w-40"><option>كل التصنيفات</option></select>
        <button className="px-6 py-2 bg-blue-700 text-white rounded">بحث</button>
      </form>
      {/* Example List */}
      <div className="grid md:grid-cols-3 gap-6">
        {[1,2,3].map(i => (
          <Link key={i} to={`/event/${i}`} className="bg-white rounded-xl shadow p-5 hover:scale-105 transition">
            <div className="font-bold mb-2">فعالية {i}</div>
            <div className="text-gray-500">دبي - 2025/08/10</div>
            <div className="text-sm text-blue-600 mt-3">عرض التفاصيل</div>
          </Link>
        ))}
      </div>
    </div>
  );
}
