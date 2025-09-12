import React from "react";
export default function MapEvents() {
  // TODO: Integrate Map API (Google Maps or Leaflet)
  return (
    <div className="max-w-4xl mx-auto py-10">
      <h2 className="text-2xl font-bold mb-6">الفعاليات على الخريطة</h2>
      <div className="bg-white rounded-xl shadow p-4 flex items-center justify-center min-h-[300px]">
        <div className="text-gray-500">سيتم عرض خريطة الفعاليات هنا (API تكامل)</div>
      </div>
    </div>
  );
}
