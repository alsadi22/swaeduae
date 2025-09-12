import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function Login() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="flex flex-col items-center justify-center min-h-[60vh]">
        <form className="bg-white shadow-lg rounded-2xl p-10 w-full max-w-sm">
          <h2 className="text-center text-2xl font-bold mb-8">تسجيل الدخول</h2>
          <label className="block mb-2 text-right">البريد الإلكتروني</label>
          <input className="w-full mb-4 p-2 border rounded" type="email" />
          <label className="block mb-2 text-right">كلمة المرور</label>
          <input className="w-full mb-6 p-2 border rounded" type="password" />
          <button className="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">دخول</button>
          <div className="text-center mt-4">
            مستخدم جديد؟ <a href="/signup" className="text-blue-700 hover:underline">سجل هنا</a>
          </div>
        </form>
      </div>
      <Footer />
    </div>
  );
}
