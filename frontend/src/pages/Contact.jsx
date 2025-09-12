import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function Contact() {
  return (
    <div dir="rtl">
      <Header />
      <main className="max-w-xl mx-auto py-10 px-4">
        <h1 className="text-2xl font-bold mb-6 text-blue-900">تواصل معنا</h1>
        <form className="bg-white rounded-xl shadow p-6 space-y-5">
          <input type="text" placeholder="الاسم الكامل" className="w-full border px-4 py-2 rounded" />
          <input type="email" placeholder="البريد الإلكتروني" className="w-full border px-4 py-2 rounded" />
          <textarea placeholder="رسالتك" className="w-full border px-4 py-2 rounded h-32" />
          <button type="submit" className="bg-orange-500 text-white px-6 py-2 rounded font-bold hover:bg-orange-600 w-full">إرسال</button>
        </form>
        <div className="mt-8 text-sm text-gray-600">
          <p>أو عبر البريد: <span className="font-bold text-blue-900">info@swaeduae.ae</span></p>
          <p>الهاتف: <span className="font-bold text-blue-900">800-VOLAE</span></p>
        </div>
      </main>
      <Footer />
    </div>
  );
}
