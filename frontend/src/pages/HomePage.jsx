import React from "react";
import { Link } from "react-router-dom";

export default function HomePage() {
  return (
    <div className="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-50 to-blue-200 text-center">
      <header className="mb-10">
        <h1 className="text-4xl md:text-6xl font-bold text-blue-700 mb-2">سواعد الإمارات</h1>
        <p className="text-lg md:text-2xl text-blue-900 mb-6">منصة التطوع الوطنية الأولى في الإمارات</p>
        <div>
          <Link to="/events" className="px-6 py-2 bg-blue-600 rounded-xl text-white shadow font-bold hover:bg-blue-700 mx-2">تصفح الفعاليات</Link>
          <Link to="/signup" className="px-6 py-2 bg-green-600 rounded-xl text-white shadow font-bold hover:bg-green-700 mx-2">سجل كمتطوع</Link>
        </div>
      </header>
      <section className="mb-8 w-full max-w-4xl bg-white p-6 rounded-2xl shadow">
        <h2 className="text-2xl font-bold mb-2">نبذة عن المنصة</h2>
        <p>شارك في الفعاليات التطوعية، وساهم في بناء مجتمع أفضل، واحصل على شهادات معتمدة وساعات تطوعية.</p>
      </section>
      <section className="mb-10 w-full max-w-4xl flex flex-wrap justify-around gap-6">
        <StatCard label="عدد المتطوعين" value="15,000+" />
        <StatCard label="عدد الفعاليات" value="320+" />
        <StatCard label="شهادات مُصدرة" value="6,200+" />
        <StatCard label="ساعات التطوع" value="41,800+" />
      </section>
      <footer className="mt-auto py-4 text-sm text-gray-500">© {new Date().getFullYear()} سواعد الإمارات</footer>
    </div>
  );
}
function StatCard({ label, value }) {
  return (
    <div className="bg-blue-100 px-6 py-3 rounded-xl shadow text-center flex flex-col items-center min-w-[150px]">
      <div className="text-2xl font-bold text-blue-800 mb-1">{value}</div>
      <div className="text-blue-600">{label}</div>
    </div>
  );
}
