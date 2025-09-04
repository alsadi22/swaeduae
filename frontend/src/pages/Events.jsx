import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
const events = [
  {
    id: 1,
    title: "زيارة منزل المسنين",
    city: "ABU DHABI AL AIN",
    image: "/img/sample1.jpg",
    date: "Dec 01, 2024",
    volunteers: 405
  },
  {
    id: 2,
    title: "الرسم مع سلامة",
    city: "ABU DHABI AL AIN",
    image: "/img/sample2.jpg",
    date: "Jul 31 - Aug 21, 2025",
    volunteers: 30
  },
  {
    id: 3,
    title: "متطوع متخصص - المجالات الرياضية والصحية",
    city: "ABU DHABI CITY",
    image: "/img/sample3.jpg",
    date: "Jan 1 - Dec 1, 2025",
    volunteers: 300
  }
];
export default function Events() {
  return (
    <div className="min-h-screen bg-gray-100">
      <Header />
      <div className="max-w-6xl mx-auto mt-10 px-4">
        <h2 className="text-2xl font-bold mb-6 text-blue-900">جميع الفرص التطوعية</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {events.map(ev=>(
            <div key={ev.id} className="bg-white rounded-xl shadow-lg p-6">
              <img src={ev.image} alt="" className="rounded-xl mb-3 h-36 w-full object-cover"/>
              <div className="font-bold mb-1">{ev.title}</div>
              <div className="text-gray-500 mb-1">{ev.date} - {ev.city}</div>
              <div className="text-xs mb-2">{ev.volunteers} عدد المتطوعين</div>
              <a href={`/events/${ev.id}`} className="block bg-blue-600 text-white px-6 py-2 rounded-xl font-bold text-center hover:bg-blue-700">سجل الآن</a>
            </div>
          ))}
        </div>
      </div>
      <Footer />
    </div>
  );
}
