import React from "react";
import { Link } from "react-router-dom";
import Header from "../components/Header";
import Footer from "../components/Footer";
const events = [
  {
    id: 1,
    title: "زيارة منزل المسنين",
    city: "ABU DHABI AL AIN",
    image: "/img/sample1.jpg",
    date_start: "Dec 01, 2024",
    description: "زيارة كبار السن من المواطنين بقيادة مستشفى طوان..."
  },
  {
    id: 2,
    title: "الرسم مع سلامة",
    city: "ABU DHABI AL AIN",
    image: "/img/sample2.jpg",
    date_start: "Jul 31 - Aug 21, 2025",
    description: "يدعوكم فريق الهمة التطوعي بالتعاون مع مكتبة زايد للمشاركة في فعالية..."
  },
  {
    id: 3,
    title: "متطوع متخصص - المجالات الرياضية والصحية",
    city: "ABU DHABI CITY",
    image: "/img/sample3.jpg",
    date_start: "Jan 1 - Dec 1, 2025",
    description: "نحن نبحث عن متطوعين متخصصين في مجال الرياضة والصحة والعافية..."
  }
];
export default function Home() {
  return (
    <div className="bg-gradient-to-b from-blue-900 to-blue-400 min-h-screen">
      <Header />
      {/* Hero */}
      <div className="text-center py-20 bg-blue-800 bg-opacity-80">
        <h1 className="text-4xl font-bold mb-2 text-white">خطوة أولى لإحداث الفارق</h1>
        <p className="text-white text-lg mb-10">أكبر منصة تطوعية على مستوى دولة الإمارات العربية المتحدة</p>
        <form className="flex flex-col md:flex-row gap-4 justify-center max-w-2xl mx-auto mb-4">
          <input className="rounded px-5 py-2" placeholder="فرصة أو جهة أو مدينة..." />
          <button className="bg-green-600 px-6 rounded-xl font-bold text-white">بحث</button>
        </form>
        <div className="flex justify-center gap-8 mb-4">
          <Link to="/events" className="bg-white text-blue-800 px-6 py-2 rounded-xl shadow font-bold hover:bg-gray-100">جميع الفرص</Link>
          <Link to="/organizations" className="bg-white text-blue-800 px-6 py-2 rounded-xl shadow font-bold hover:bg-gray-100">كل الجهات</Link>
        </div>
      </div>
      {/* Featured Events */}
      <div className="max-w-6xl mx-auto mt-12 px-4">
        <h2 className="text-2xl font-bold mb-6">فرص تطوعية مميزة</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {events.map(ev=>(
            <Link to={`/events/${ev.id}`} key={ev.id} className="bg-white rounded-2xl shadow-xl p-6 hover:scale-105 duration-100">
              <img src={ev.image} alt="" className="rounded-xl mb-3 h-36 w-full object-cover"/>
              <div className="font-bold mb-1">{ev.title}</div>
              <div className="text-gray-500 mb-2">{ev.date_start} - {ev.city}</div>
              <div className="text-xs">{ev.description.slice(0,80)}...</div>
            </Link>
          ))}
        </div>
        <div className="mt-8 text-center">
          <Link to="/events" className="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700">عرض كل الفرص</Link>
        </div>
      </div>
      <Footer />
    </div>
  );
}
