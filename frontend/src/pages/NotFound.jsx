import React from "react";
import { Link } from "react-router-dom";
export default function NotFound() {
  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-50">
      <div className="text-[80px] font-black text-blue-800 mb-3">404</div>
      <div className="text-xl mb-4">الصفحة غير موجودة</div>
      <Link to="/" className="py-2 px-6 bg-blue-700 text-white rounded hover:bg-blue-900">العودة للصفحة الرئيسية</Link>
    </div>
  );
}
