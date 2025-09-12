import React, { useState } from "react";
import { useParams } from "react-router-dom";
export default function ResetPassword() {
  const { token } = useParams();
  const [pw, setPw] = useState("");
  const [ok, setOk] = useState(false);
  const submit = e => { e.preventDefault(); fetch(`/api/reset/${token}`, {method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({password:pw})}).then(()=>setOk(true)); };
  if (ok) return <div className="mt-10 text-center text-green-700 font-bold">تم إعادة تعيين كلمة المرور!</div>;
  return (
    <form className="max-w-md mx-auto mt-10 bg-white p-8 rounded-2xl shadow" onSubmit={submit}>
      <h1 className="text-2xl font-bold mb-6">تعيين كلمة مرور جديدة</h1>
      <input className="w-full mb-6 p-3 rounded-xl border" type="password" placeholder="كلمة المرور الجديدة" required value={pw} onChange={e=>setPw(e.target.value)} />
      <button className="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">تعيين</button>
    </form>
  );
}
