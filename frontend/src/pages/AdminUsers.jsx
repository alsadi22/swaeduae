import React, { useEffect, useState } from "react";
import axios from "axios";
export default function AdminUsers() {
  const [users, setUsers] = useState([]);
  useEffect(() => { axios.get("/api/admin/users").then(r => setUsers(r.data)); }, []);
  return (
    <div className="p-6">
      <h2 className="font-bold text-2xl mb-4">إدارة المستخدمين</h2>
      <table className="w-full border mb-6">
        <thead><tr><th>الاسم</th><th>البريد</th><th>الدور</th><th>الحالة</th></tr></thead>
        <tbody>
          {users.map(u => (
            <tr key={u.id}><td>{u.name}</td><td>{u.email}</td><td>{u.role}</td><td>{u.is_banned ? "محظور" : "نشط"}</td></tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
