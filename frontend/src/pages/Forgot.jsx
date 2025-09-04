import React, { useState } from "react";
import { Link } from "react-router-dom";
export default function Forgot() {
  const [email, setEmail] = useState("");
  const [sent, setSent] = useState(false);

  const handleForgot = e => {
    e.preventDefault();
    // TODO: connect backend for reset
    setSent(true);
  };

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-50">
      <div className="w-full max-w-md p-8 bg-white rounded-lg shadow">
        <h2 className="text-2xl font-bold mb-4 text-center">استعادة كلمة المرور</h2>
        {sent ? (
          <div className="mb-4 text-green-700">تم إرسال رابط الاستعادة إذا كان البريد مسجلاً</div>
        ) : (
          <form onSubmit={handleForgot} className="space-y-4">
            <input type="email" className="w-full p-2 border rounded" placeholder="البريد الإلكتروني" value={email} onChange={e => setEmail(e.target.value)} />
            <button type="submit" className="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">إرسال</button>
          </form>
        )}
        <div className="mt-4 flex justify-between">
          <Link to="/login" className="text-blue-600">العودة للدخول</Link>
        </div>
      </div>
    </div>
  );
}
