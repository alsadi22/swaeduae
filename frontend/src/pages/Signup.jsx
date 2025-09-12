import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function Signup() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="flex flex-col items-center justify-center min-h-[60vh]">
        <form className="bg-white shadow-lg rounded-2xl p-10 w-full max-w-sm">
          <h2 className="text-center text-2xl font-bold mb-8">إنشاء حساب جديد</h2>
          <label className="block mb-2 text-right">الاسم الكامل</label>
          <input className="w-full mb-3 p-2 border rounded" type="text" />
          <label className="block mb-2 text-right">البريد الإلكتروني</label>
          <input className="w-full mb-3 p-2 border rounded" type="email" />
          <label className="block mb-2 text-right">كلمة المرور</label>
          <input className="w-full mb-6 p-2 border rounded" type="password" />
          <button className="w-full bg-green-600 text-white py-2 rounded font-bold hover:bg-green-700">تسجيل</button>
        </form>
      </div>
      <Footer />
    </div>
  );
}
