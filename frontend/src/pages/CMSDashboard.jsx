import React, { useState } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
const dummyEvents = [
  { id: 1, name: "زيارة منزل المسنين", location: "أبوظبي", date: "2025-12-01" },
  { id: 2, name: "الرسم مع سلامة", location: "العين", date: "2025-08-10" },
];
export default function CMSDashboard() {
  const [events, setEvents] = useState(dummyEvents);
  const [newEvent, setNewEvent] = useState({ name: "", location: "", date: "" });
  const [editing, setEditing] = useState(null);

  const handleAdd = () => {
    setEvents([...events, { ...newEvent, id: events.length + 1 }]);
    setNewEvent({ name: "", location: "", date: "" });
  };
  const handleDelete = (id) => setEvents(events.filter((e) => e.id !== id));
  const handleEdit = (e) => setEditing(e);
  const handleUpdate = () => {
    setEvents(events.map(ev => ev.id === editing.id ? editing : ev));
    setEditing(null);
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-3xl mx-auto mt-8 bg-white rounded-xl shadow-lg p-8">
        <h2 className="text-2xl font-bold mb-4">لوحة إدارة الفعاليات (CMS)</h2>
        <div className="mb-6">
          <input className="border rounded px-2 py-1 mx-2" placeholder="اسم الفعالية" value={editing ? editing.name : newEvent.name} onChange={e => editing ? setEditing({ ...editing, name: e.target.value }) : setNewEvent({ ...newEvent, name: e.target.value })} />
          <input className="border rounded px-2 py-1 mx-2" placeholder="الموقع" value={editing ? editing.location : newEvent.location} onChange={e => editing ? setEditing({ ...editing, location: e.target.value }) : setNewEvent({ ...newEvent, location: e.target.value })} />
          <input className="border rounded px-2 py-1 mx-2" type="date" value={editing ? editing.date : newEvent.date} onChange={e => editing ? setEditing({ ...editing, date: e.target.value }) : setNewEvent({ ...newEvent, date: e.target.value })} />
          {editing ? (
            <button className="bg-blue-600 text-white rounded px-3 py-1 mx-1" onClick={handleUpdate}>تحديث</button>
          ) : (
            <button className="bg-green-600 text-white rounded px-3 py-1 mx-1" onClick={handleAdd}>إضافة</button>
          )}
        </div>
        <table className="w-full mb-6">
          <thead>
            <tr className="bg-gray-100">
              <th>اسم الفعالية</th>
              <th>الموقع</th>
              <th>التاريخ</th>
              <th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            {events.map(ev => (
              <tr key={ev.id} className="border-t">
                <td>{ev.name}</td>
                <td>{ev.location}</td>
                <td>{ev.date}</td>
                <td>
                  <button className="text-blue-600 mx-1" onClick={() => handleEdit(ev)}>تعديل</button>
                  <button className="text-red-600 mx-1" onClick={() => handleDelete(ev.id)}>حذف</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <Footer />
    </div>
  );
}
