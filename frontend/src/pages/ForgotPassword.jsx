import React, { useState } from "react";
export default function ForgotPassword() {
  const [email, setEmail] = useState("");
  const [ok, setOk] = useState(false);
  const submit = e => { e.preventDefault(); fetch("/api/forgot", {method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({email})}).then(()=>setOk(true)); };
  if (ok) return <div className="mt-10 text-center text-green-700 font-bold">تم إرسال رابط إعادة تعيين كلمة المرور!</div>;
  return (
    <form className="max-w-md mx-auto mt-10 bg-white p-8 rounded-2xl shadow" onSubmit={submit}>
      <h1 className="text-2xl font-bold mb-6">نسيت كلمة المرور؟</h1>
      <input className="w-full mb-6 p-3 rounded-xl border" type="email" placeholder="البريد الإلكتروني" required value={email} onChange={e=>setEmail(e.target.value)} />
      <button className="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">إرسال</button>
    </form>
  );
}
