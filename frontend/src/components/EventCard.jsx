import React from "react";
export default function EventCard({ title, desc, location, date, time, category, img, volunteers }) {
  return (
    <div className="bg-white shadow rounded-lg overflow-hidden flex flex-col justify-between">
      <img src={img || "/events/placeholder.jpg"} alt={title} className="h-40 w-full object-cover"/>
      <div className="p-4 flex-1 flex flex-col justify-between">
        <div>
          <span className="inline-block bg-blue-100 text-blue-800 text-xs rounded px-2 py-1 mb-1 font-bold">{category}</span>
          <h2 className="font-bold text-lg mb-1 text-blue-900">{title}</h2>
          <p className="text-gray-700 text-sm mb-3">{desc}</p>
        </div>
        <div className="text-xs text-gray-600 mb-2">
          <span className="block mb-1">المكان: {location}</span>
          <span className="block mb-1">التاريخ: {date}</span>
          <span className="block mb-1">الوقت: {time}</span>
          <span className="block">عدد المتطوعين: {volunteers}</span>
        </div>
        <button className="bg-orange-500 text-white w-full mt-2 py-2 rounded font-bold hover:bg-orange-600 transition">سجل الآن</button>
      </div>
    </div>
  );
}
