import React from "react";
import { useParams } from "react-router-dom";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function EventDetails() {
  const { id } = useParams();
  // Example static data; in production, fetch by id
  const event = {
    id,
    title: "زيارة منزل المسنين",
    description: "تفاصيل هذه المبادرة...",
    image: "/img/sample1.jpg",
    city: "ABU DHABI AL AIN",
    date: "Dec 01, 2024",
    volunteers: 405
  };
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-xl">
        <img src={event.image} alt="" className="rounded-xl mb-4 w-full h-64 object-cover"/>
        <div className="text-3xl font-bold mb-2">{event.title}</div>
        <div className="text-gray-500 mb-2">{event.date} - {event.city}</div>
        <div className="mb-4">{event.description}</div>
        <button className="bg-blue-700 text-white px-7 py-3 rounded-xl font-bold hover:bg-blue-800">سجل كمتطوع</button>
      </div>
      <Footer />
    </div>
  );
}
