import React, { useState } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function Messaging() {
  const [messages, setMessages] = useState([
    { from: "Admin", text: "مرحباً بك في منصة سواعد الإمارات!" },
    { from: "User", text: "شكراً لكم" },
  ]);
  const [input, setInput] = useState("");
  const sendMsg = () => {
    if (input.trim()) {
      setMessages([...messages, { from: "Admin", text: input }]);
      setInput("");
    }
  };
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-2xl mx-auto mt-8 bg-white rounded-xl shadow-lg p-8">
        <h2 className="text-2xl font-bold mb-4">الرسائل</h2>
        <div className="h-48 overflow-y-auto border rounded mb-4 bg-gray-50 p-4">
          {messages.map((msg, i) => (
            <div key={i} className={msg.from === "Admin" ? "text-right" : "text-left"}>
              <span className="font-bold">{msg.from}: </span>
              <span>{msg.text}</span>
            </div>
          ))}
        </div>
        <div className="flex gap-2">
          <input className="flex-1 border rounded px-2 py-1" value={input} onChange={e => setInput(e.target.value)} placeholder="اكتب رسالة..." />
          <button className="bg-blue-600 text-white rounded px-4" onClick={sendMsg}>إرسال</button>
        </div>
      </div>
      <Footer />
    </div>
  );
}
