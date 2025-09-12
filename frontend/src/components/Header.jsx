import React from "react";
import { Link } from "react-router-dom";
export default function Header() {
  return (
    <div className="bg-blue-900 text-white py-2">
      <div className="flex justify-between items-center max-w-6xl mx-auto px-4">
        <img src="/img/logo.svg" alt="logo" className="h-12"/>
        <div className="flex gap-5 items-center">
          <Link to="/" className="hover:underline">الرئيسية</Link>
          <Link to="/about" className="hover:underline">عن المنصة</Link>
          <Link to="/events" className="hover:underline">الفرص</Link>
          <Link to="/organizations" className="hover:underline">جهات</Link>
          <Link to="/profile" className="hover:underline">الملف الشخصي</Link>
          <Link to="/admin" className="hover:underline text-yellow-400 font-bold">لوحة الإدارة</Link>
          <Link to="/login" className="bg-orange-500 rounded px-5 py-1 font-bold hover:bg-orange-600">تسجيل الدخول</Link>
          <Link to="/signup" className="bg-white text-blue-900 rounded px-5 py-1 font-bold hover:bg-gray-200">تسجيل جديد</Link>
        </div>
      </div>
    </div>
  );
}
